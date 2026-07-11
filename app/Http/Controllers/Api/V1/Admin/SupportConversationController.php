<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Events\SupportMessageSent;
use App\Http\Controllers\Controller;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportConversationController extends Controller
{
    public function index(): JsonResponse
    {
        $conversations = SupportConversation::with(['user', 'lastMessage.sender'])
            ->latest('last_message_at')
            ->get()
            ->map(fn ($c) => $this->formatConversation($c));

        return response()->json(['success' => true, 'data' => $conversations]);
    }

    public function show(User $user): JsonResponse
    {
        $conversation = SupportConversation::firstOrCreate(['user_id' => $user->id]);
        $conversation->load(['user', 'lastMessage.sender']);

        return response()->json(['success' => true, 'data' => $this->formatConversation($conversation)]);
    }

    public function messages(SupportConversation $supportConversation): JsonResponse
    {
        $messages = $supportConversation->messages()
            ->with('sender')
            ->oldest()
            ->get()
            ->map(fn ($m) => $this->formatMessage($m));

        $supportConversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true, 'data' => $messages]);
    }

    public function sendMessage(Request $request, SupportConversation $supportConversation): JsonResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:2000']]);

        $message = $supportConversation->messages()->create([
            'sender_id' => Auth::id(),
            'body' => $data['body'],
        ]);

        $supportConversation->update(['last_message_at' => now()]);
        $message->load('sender');

        SupportMessageSent::dispatch($message);

        return response()->json(['success' => true, 'data' => $this->formatMessage($message)], 201);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatConversation(SupportConversation $c): array
    {
        $last = $c->lastMessage;

        return [
            'id' => $c->id,
            'user' => [
                'id' => $c->user->id,
                'name' => $c->user->name,
                'avatar' => $c->user->avatar,
                'role' => $c->user->role,
            ],
            'last_message' => $last ? [
                'id' => $last->id,
                'body' => $last->body,
                'sender_id' => $last->sender_id,
                'created_at' => $last->created_at->toISOString(),
            ] : null,
            'last_message_at' => $c->last_message_at?->toISOString(),
            'created_at' => $c->created_at->toISOString(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formatMessage(SupportMessage $m): array
    {
        return [
            'id' => $m->id,
            'support_conversation_id' => $m->support_conversation_id,
            'sender_id' => $m->sender_id,
            'sender_name' => $m->sender->name,
            'sender_avatar' => $m->sender->avatar,
            'sender_role' => $m->sender->role,
            'body' => $m->body,
            'read_at' => $m->read_at?->toISOString(),
            'created_at' => $m->created_at->toISOString(),
        ];
    }
}
