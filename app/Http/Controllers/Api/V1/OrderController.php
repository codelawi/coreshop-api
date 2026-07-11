<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Mail\OrderCompletedMail;
use App\Models\Order;
use App\Services\ExpoPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function __construct(private readonly ExpoPushService $push) {}

    public function index(Request $request): OrderCollection
    {
        $orders = Order::with(['client', 'coupon', 'items', 'store.seller'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, fn ($q) => $q->whereHas('client', fn ($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%")
            ))
            ->latest()
            ->paginate($request->per_page ?? 10);

        return new OrderCollection($orders);
    }

    public function show(Order $order): JsonResponse
    {
        $order->load(['client', 'coupon', 'items.product', 'store', 'address', 'driver']);

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order),
        ]);
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:pending,approved,preparing,ready_for_pickup,assigned,out_for_delivery,delivered,completed,cancelled,refunded'],
        ]);

        $order->update(['status' => $request->status]);

        $order->load(['client', 'store.seller', 'items', 'address', 'coupon']);

        $this->notifyClient($order, $request->status);
        $this->notifySeller($order, $request->status);

        if ($request->status === 'completed') {
            $this->sendCompletionEmails($order);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => new OrderResource($order),
        ]);
    }

    private function notifyClient(Order $order, string $status): void
    {
        if (! $order->client) {
            return;
        }

        $lang = $order->client->language ?? 'ar';
        $id = $order->id;

        $messages = $lang === 'ar' ? [
            'approved' => ['تمت الموافقة على طلبك!', "تمت الموافقة على طلبك رقم #{$id} وسيتم تحضيره قريبًا."],
            'preparing' => ['جاري التحضير', "البائع يحضّر طلبك رقم #{$id}."],
            'ready_for_pickup' => ['جاهز للاستلام', "طلبك رقم #{$id} جاهز وينتظر السائق."],
            'assigned' => ['تم تعيين السائق', "تم تعيين سائق لطلبك رقم #{$id}."],
            'out_for_delivery' => ['في الطريق!', "طلبك رقم #{$id} في طريقه إليك."],
            'delivered' => ['تم التوصيل!', "تم توصيل طلبك رقم #{$id}. استمتع بتجربتك!"],
            'completed' => ['اكتمل الطلب', "اكتمل طلبك رقم #{$id}. شكرًا لك!"],
            'cancelled' => ['تم إلغاء الطلب', "تم إلغاء طلبك رقم #{$id}."],
            'refunded' => ['تم استرداد المبلغ', "تم استرداد مبلغ طلبك رقم #{$id}."],
        ] : [
            'approved' => ['Order Approved!', "Your order #{$id} has been approved and will be prepared soon."],
            'preparing' => ['Being Prepared', "The seller is preparing your order #{$id}."],
            'ready_for_pickup' => ['Ready for Pickup', "Your order #{$id} is ready and waiting for a driver."],
            'assigned' => ['Driver Assigned', "A driver has been assigned to your order #{$id}."],
            'out_for_delivery' => ['On the Way!', "Your order #{$id} is out for delivery."],
            'delivered' => ['Order Delivered!', "Your order #{$id} has been delivered. Enjoy!"],
            'completed' => ['Order Completed', "Your order #{$id} has been completed. Thank you!"],
            'cancelled' => ['Order Cancelled', "Your order #{$id} has been cancelled."],
            'refunded' => ['Order Refunded', "Your order #{$id} has been refunded."],
        ];

        if (! isset($messages[$status])) {
            return;
        }

        [$title, $body] = $messages[$status];

        $this->push->sendToUser($order->client, $title, $body, [
            'type' => 'order_status',
            'order_id' => $order->id,
            'status' => $status,
        ]);
    }

    private function notifySeller(Order $order, string $status): void
    {
        $seller = $order->store?->seller;

        if (! $seller) {
            return;
        }

        $lang = $seller->language ?? 'ar';
        $id = $order->id;

        $messages = $lang === 'ar' ? [
            'assigned' => ['تم تعيين السائق', "تم تعيين سائق للطلب رقم #{$id}."],
            'out_for_delivery' => ['خرج للتوصيل', "الطلب رقم #{$id} خرج للتوصيل."],
            'delivered' => ['تم التوصيل', "تم تسليم الطلب رقم #{$id} للعميل."],
            'completed' => ['اكتمل الطلب', "اكتمل الطلب رقم #{$id}."],
            'cancelled' => ['تم إلغاء الطلب', "تم إلغاء الطلب رقم #{$id} من قبل الإدارة."],
            'refunded' => ['تم الاسترداد', "تم استرداد مبلغ الطلب رقم #{$id} من قبل الإدارة."],
        ] : [
            'assigned' => ['Driver Assigned', "A driver has been assigned to order #{$id}."],
            'out_for_delivery' => ['Out for Delivery', "Order #{$id} is now out for delivery."],
            'delivered' => ['Order Delivered', "Order #{$id} has been delivered to the customer."],
            'completed' => ['Order Completed', "Order #{$id} has been completed."],
            'cancelled' => ['Order Cancelled', "Order #{$id} has been cancelled by the admin."],
            'refunded' => ['Order Refunded', "Order #{$id} has been refunded by the admin."],
        ];

        if (! isset($messages[$status])) {
            return;
        }

        [$title, $body] = $messages[$status];

        $this->push->sendToUser($seller, $title, $body, [
            'type' => 'order_status',
            'order_id' => $order->id,
            'status' => $status,
        ]);
    }

    private function sendCompletionEmails(Order $order): void
    {
        if ($order->client?->email) {
            Mail::to($order->client->email)
                ->queue(new OrderCompletedMail($order, 'client'));
        }

        $seller = $order->store?->seller;
        if ($seller?->email) {
            Mail::to($seller->email)
                ->queue(new OrderCompletedMail($order, 'seller'));
        }
    }
}
