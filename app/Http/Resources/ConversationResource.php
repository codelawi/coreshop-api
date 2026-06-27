<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lastMsg = $this->messages->last();

        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'last_message_at' => $this->last_message_at?->toISOString(),
            'client' => [
                'id' => $this->client->id,
                'name' => $this->client->name,
                'avatar' => $this->client->avatar,
            ],
            'store' => [
                'id' => $this->store->id,
                'name' => $this->store->name,
                'logo' => $this->store->logo,
            ],
            'last_message' => $lastMsg ? [
                'body' => $lastMsg->body,
                'sender_id' => $lastMsg->sender_id,
                'created_at' => $lastMsg->created_at->toISOString(),
            ] : null,
            'unread_count' => $this->whenLoaded('messages', fn () => $this->messages
                ->where('sender_id', '!=', $request->user()->id)
                ->whereNull('read_at')
                ->count()
            ),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
