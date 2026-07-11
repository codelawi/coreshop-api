<?php

namespace App\Events;

use App\Models\SupportMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupportMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly SupportMessage $message) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('support.'.$this->message->support_conversation_id)];
    }

    public function broadcastAs(): string
    {
        return 'SupportMessageSent';
    }

    /**
     * @return array{id: int, support_conversation_id: int, sender_id: int, sender_name: string, sender_avatar: string|null, sender_role: string, body: string, read_at: string|null, created_at: string}
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'support_conversation_id' => $this->message->support_conversation_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'sender_avatar' => $this->message->sender->avatar,
            'sender_role' => $this->message->sender->role,
            'body' => $this->message->body,
            'read_at' => $this->message->read_at?->toISOString(),
            'created_at' => $this->message->created_at->toISOString(),
        ];
    }
}
