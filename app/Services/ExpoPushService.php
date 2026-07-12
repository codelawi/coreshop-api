<?php

namespace App\Services;

use App\Jobs\SendPushNotification;
use App\Models\User;
use App\Models\UserNotification;

class ExpoPushService
{
    public function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        UserNotification::create([
            'user_id' => $user->id,
            'type' => $data['type'] ?? 'system',
            'title' => $title,
            'body' => $body,
            'data' => $data ?: null,
        ]);

        /** @var string|null $token */
        $token = $user->getAttribute('expo_push_token');

        if (empty($token)) {
            return;
        }

        SendPushNotification::dispatch($token, $title, $body, $data);
    }

    /** @param array<string> $tokens */
    public function sendBatch(array $tokens, string $title, string $body, array $data = []): void
    {
        $tokens = array_values(array_filter($tokens));

        if (empty($tokens)) {
            return;
        }

        SendPushNotification::dispatch($tokens, $title, $body, $data);
    }
}
