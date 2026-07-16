<?php

namespace App\Http\Controllers\Api\V1\Client;

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
        $conversations = Conversation::with(['store', 'client', 'messages' => fn ($q) => $q->latest()])
            ->where('client_id', Auth::id())
            ->latest('last_message_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ConversationResource::collection($conversations),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'store_id' => ['required', 'integer', 'exists:stores,id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
        ]);

        $conversation = Conversation::firstOrCreate([
            'client_id' => Auth::id(),
            'store_id' => $data['store_id'],
        ]);

        $conversation->load(['store', 'client', 'messages' => fn ($q) => $q->latest()]);

        return response()->json([
            'success' => true,
            'data' => new ConversationResource($conversation),
        ], 201);
    }

    public function messages(Conversation $conversation): JsonResponse
    {
        abort_unless($conversation->client_id === Auth::id(), 403);

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
        abort_unless($conversation->client_id === Auth::id(), 403);

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

        $seller = $conversation->store->seller;
        if ($seller) {
            $notifBody = $type === 'text'
                ? ($data['body'] ?? '')
                : __('chat.shared_a_:type', ['type' => $type]);

            $this->push->sendToUser($seller, Auth::user()->name, $notifBody, [
                'type' => 'new_message',
                'conversation_id' => $conversation->id,
                'role' => 'seller',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => new MessageResource($message),
        ], 201);
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
