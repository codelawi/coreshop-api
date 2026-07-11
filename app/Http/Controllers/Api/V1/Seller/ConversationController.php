<?php

namespace App\Http\Controllers\Api\V1\Seller;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Order;
use App\Models\Product;
use App\Services\ExpoPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function __construct(private readonly ExpoPushService $push) {}

    public function index(): JsonResponse
    {
        $store = Auth::user()->store;

        if (! $store) {
            return response()->json(['success' => false, 'message' => 'No store found.'], 404);
        }

        $conversations = Conversation::with(['client', 'store', 'messages' => fn ($q) => $q->latest()])
            ->where('store_id', $store->id)
            ->latest('last_message_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ConversationResource::collection($conversations),
        ]);
    }

    public function messages(Conversation $conversation): JsonResponse
    {
        $this->authorizeConversation($conversation);

        $messages = $conversation->messages()
            ->with('sender')
            ->oldest()
            ->get();

        $conversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'data' => MessageResource::collection($messages),
        ]);
    }

    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeConversation($conversation);

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:1000'],
            'type' => ['sometimes', 'string', 'in:text,product,order'],
            'reference_id' => ['nullable', 'integer'],
        ]);

        $type = $data['type'] ?? 'text';
        $referenceData = $this->buildReferenceData($type, $data['reference_id'] ?? null);

        $message = $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'body' => $data['body'] ?? '',
            'type' => $type,
            'reference_id' => $data['reference_id'] ?? null,
            'reference_data' => $referenceData,
        ]);

        $conversation->update(['last_message_at' => now()]);
        $message->load('sender');
        MessageSent::dispatch($message);

        $storeName = $conversation->store->name;
        $notifBody = $type === 'text'
            ? ($data['body'] ?? '')
            : "Shared a {$type}";

        $this->push->sendToUser($conversation->client, $storeName, $notifBody, [
            'type' => 'new_message',
            'conversation_id' => $conversation->id,
            'role' => 'client',
            'store_name' => $storeName,
        ]);

        return response()->json([
            'success' => true,
            'data' => new MessageResource($message),
        ], 201);
    }

    private function authorizeConversation(Conversation $conversation): void
    {
        $store = Auth::user()->store;

        abort_unless($store && $conversation->store_id === $store->id, 403, 'Unauthorized.');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildReferenceData(string $type, ?int $referenceId): ?array
    {
        if (! $referenceId) {
            return null;
        }

        if ($type === 'product') {
            $product = Product::with('productImages')->find($referenceId);
            if (! $product) {
                return null;
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'image' => $product->productImages->first()?->url ?? ($product->images[0] ?? null),
            ];
        }

        if ($type === 'order') {
            $order = Order::find($referenceId);
            if (! $order) {
                return null;
            }

            return [
                'id' => $order->id,
                'status' => $order->status,
                'total' => (float) $order->total,
                'created_at' => $order->created_at->toISOString(),
            ];
        }

        return null;
    }
}
