<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendPushNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 10;

    /** @param array<string>|string $tokens */
    public function __construct(
        public readonly array|string $tokens,
        public readonly string $title,
        public readonly string $body,
        public readonly array $data = [],
    ) {}

    public function handle(): void
    {
        $tokens = is_array($this->tokens) ? array_values(array_filter($this->tokens)) : [$this->tokens];

        if (empty($tokens)) {
            return;
        }

        $messages = array_map(fn (string $token) => [
            'to' => $token,
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
            'sound' => 'default',
            'priority' => 'high',
            'channelId' => 'coreshop_v2',
        ], $tokens);

        Http::withHeaders(['Accept-Encoding' => 'gzip, deflate'])
            ->post('https://exp.host/--/api/v2/push/send', count($messages) === 1 ? $messages[0] : $messages)
            ->throw();
    }

    public function failed(\Throwable $e): void
    {
        Log::warning('Expo push job failed', [
            'tokens' => $this->tokens,
            'error' => $e->getMessage(),
        ]);
    }
}
