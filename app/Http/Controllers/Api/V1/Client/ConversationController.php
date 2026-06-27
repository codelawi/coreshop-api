<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
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
            'order_id' => $data['order_id'] ?? null,
        ]);

        $conversation->load(['store', 'client', 'messages' => fn ($q) => $q->latest()]);

        return response()->json([
            'success' => true,
            'data' => new ConversationResource($conversation),
        ], 201);
    }

    public function messages(Request $request, Conversation $conversation): JsonResponse
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
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'body' => $data['body'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        $message->load('sender');

        return response()->json([
            'success' => true,
            'data' => new MessageResource($message),
        ], 201);
    }
}
