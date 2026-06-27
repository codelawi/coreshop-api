<?php

namespace App\Http\Controllers\Api\V1\Seller;

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

    public function messages(Request $request, Conversation $conversation): JsonResponse
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

    private function authorizeConversation(Conversation $conversation): void
    {
        $store = Auth::user()->store;

        abort_unless($store && $conversation->store_id === $store->id, 403, 'Unauthorized.');
    }
}
