<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Events\SupportMessageSent;
use App\Http\Controllers\Controller;
use App\Models\SupportConversation;
use App\Models\SupportMessage;
use App\Services\ExpoPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function __construct(private readonly ExpoPushService $push) {}

    public function conversation(): JsonResponse
    {
        $conversation = SupportConversation::firstOrCreate(['user_id' => Auth::id()]);

        return response()->json([
            'success' => true,
            'data' => ['id' => $conversation->id],
        ]);
    }

    public function messages(SupportConversation $supportConversation): JsonResponse
    {
        abort_unless($supportConversation->user_id === Auth::id(), 403);

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
        abort_unless($supportConversation->user_id === Auth::id(), 403);

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
