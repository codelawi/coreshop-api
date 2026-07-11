<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Message $message) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('conversation.'.$this->message->conversation_id)];
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    /**
     * @return array{id: int, conversation_id: int, sender_id: int, sender_name: string, sender_avatar: string|null, body: string, type: string, reference_id: int|null, reference_data: array<string,mixed>|null, read_at: string|null, created_at: string}
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'sender_avatar' => $this->message->sender->avatar,
            'body' => $this->message->body,
            'type' => $this->message->type ?? 'text',
            'reference_id' => $this->message->reference_id,
            'reference_data' => $this->message->reference_data,
            'read_at' => $this->message->read_at?->toISOString(),
            'created_at' => $this->message->created_at->toISOString(),
        ];
    }
}
