<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoPushService
{
    private const EXPO_PUSH_URL = 'https://exp.host/--/api/v2/push/send';

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

        $this->send($token, $title, $body, $data);
    }

    public function send(string $token, string $title, string $body, array $data = []): void
    {
        try {
            Http::withHeaders(['Accept-Encoding' => 'gzip, deflate'])
                ->post(self::EXPO_PUSH_URL, [
                    'to' => $token,
                    'title' => $title,
                    'body' => $body,
                    'data' => $data,
                    'sound' => 'default',
                    'priority' => 'high',
                    'channelId' => 'coreshop_alerts',
                ]);
        } catch (\Throwable $e) {
            Log::warning('Expo push failed', ['token' => $token, 'error' => $e->getMessage()]);
        }
    }

    /**
     * @param  array<string>  $tokens
     */
    public function sendBatch(array $tokens, string $title, string $body, array $data = []): void
    {
        $tokens = array_filter($tokens);

        if (empty($tokens)) {
            return;
        }

        $messages = array_map(fn (string $token) => [
            'to' => $token,
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'sound' => 'default',
            'priority' => 'high',
            'channelId' => 'coreshop_alerts',
        ], array_values($tokens));

        try {
            Http::withHeaders(['Accept-Encoding' => 'gzip, deflate'])
                ->post(self::EXPO_PUSH_URL, $messages);
        } catch (\Throwable $e) {
            Log::warning('Expo push batch failed', ['error' => $e->getMessage()]);
        }
    }
}
