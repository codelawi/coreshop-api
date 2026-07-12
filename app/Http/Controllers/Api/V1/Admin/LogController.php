<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogController extends Controller
{
    private const LEVELS = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'level' => ['nullable', 'string', 'in:emergency,alert,critical,error,warning,notice,info,debug'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:500'],
            'search' => ['nullable', 'string', 'max:200'],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $limit = (int) $request->get('limit', 100);
        $levelFilter = strtolower($request->get('level', ''));
        $search = $request->get('search', '');
        $date = $request->get('date', now()->toDateString());

        $logPath = $this->resolveLogPath($date);

        if (! $logPath || ! file_exists($logPath)) {
            return response()->json([
                'success' => true,
                'data' => [],
                'meta' => [
                    'total' => 0,
                    'file_size_kb' => 0,
                    'date' => $date,
                    'available_dates' => $this->availableDates(),
                    'levels' => self::LEVELS,
                ],
            ]);
        }

        $entries = $this->parseLog($logPath, $limit * 3);

        if ($levelFilter) {
            $entries = array_filter($entries, fn ($e) => $e['level'] === $levelFilter);
        }

        if ($search) {
            $entries = array_filter($entries, fn ($e) => stripos($e['message'], $search) !== false);
        }

        $entries = array_slice(array_values($entries), 0, $limit);

        return response()->json([
            'success' => true,
            'data' => $entries,
            'meta' => [
                'total' => count($entries),
                'file_size_kb' => round(filesize($logPath) / 1024, 1),
                'date' => $date,
                'available_dates' => $this->availableDates(),
                'levels' => self::LEVELS,
            ],
        ]);
    }

    public function clear(Request $request): JsonResponse
    {
        $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $date = $request->get('date', now()->toDateString());
        $logPath = $this->resolveLogPath($date);

        if ($logPath && file_exists($logPath)) {
            file_put_contents($logPath, '');
        }

        return response()->json([
            'success' => true,
            'message' => "Log cleared for {$date}.",
        ]);
    }

    /** @return array<string> */
    private function availableDates(): array
    {
        $files = glob(storage_path('logs/laravel-*.log')) ?: [];

        return collect($files)
            ->map(fn ($f) => preg_replace('/.*laravel-(.+)\.log$/', '$1', $f))
            ->filter(fn ($d) => preg_match('/^\d{4}-\d{2}-\d{2}$/', $d))
            ->sortDesc()
            ->take(14)
            ->values()
            ->all();
    }

    private function resolveLogPath(string $date): ?string
    {
        // Daily rotation: storage/logs/laravel-YYYY-MM-DD.log
        $daily = storage_path("logs/laravel-{$date}.log");
        if (file_exists($daily)) {
            return $daily;
        }

        // Fallback: single file
        $single = storage_path('logs/laravel.log');
        if (file_exists($single)) {
            return $single;
        }

        return null;
    }

    /** @return array<int, array{timestamp: string, level: string, message: string, context: array<mixed>|null}> */
    private function parseLog(string $path, int $limit): array
    {
        $handle = fopen($path, 'r');
        if (! $handle) {
            return [];
        }

        // Read only last ~512KB to stay memory-safe on large files
        $maxBytes = 512 * 1024;
        if (filesize($path) > $maxBytes) {
            fseek($handle, -$maxBytes, SEEK_END);
            fgets($handle); // skip partial first line
        }

        $entries = [];
        $currentEntry = null;

        while (($line = fgets($handle)) !== false) {
            $line = rtrim($line);

            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \w+\.(\w+): (.+)$/', $line, $m)) {
                if ($currentEntry) {
                    $entries[] = $currentEntry;
                }

                $message = $m[3];
                $context = null;

                if (($jsonStart = strpos($message, ' {"')) !== false) {
                    $jsonStr = substr($message, $jsonStart + 1);
                    $decoded = json_decode($jsonStr, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $message = substr($message, 0, $jsonStart);
                        $context = $decoded;
                    }
                }

                $currentEntry = [
                    'timestamp' => $m[1],
                    'level' => strtolower($m[2]),
                    'message' => trim($message),
                    'context' => $context,
                ];
            }
        }

        if ($currentEntry) {
            $entries[] = $currentEntry;
        }

        fclose($handle);

        return array_slice(array_reverse($entries), 0, $limit);
    }
}
