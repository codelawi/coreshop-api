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
        ]);

        $logPath = storage_path('logs/laravel.log');

        if (! file_exists($logPath)) {
            return response()->json([
                'success' => true,
                'data' => [],
                'meta' => ['total' => 0, 'file_size_kb' => 0],
            ]);
        }

        $limit = (int) $request->get('limit', 100);
        $levelFilter = strtolower($request->get('level', ''));
        $search = $request->get('search', '');

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
                'levels' => self::LEVELS,
            ],
        ]);
    }

    public function clear(): JsonResponse
    {
        $logPath = storage_path('logs/laravel.log');

        if (file_exists($logPath)) {
            file_put_contents($logPath, '');
        }

        return response()->json([
            'success' => true,
            'message' => 'Log file cleared.',
        ]);
    }

    /** @return array<int, array{timestamp: string, level: string, message: string, context: array<mixed>|null}> */
    private function parseLog(string $path, int $limit): array
    {
        $handle = fopen($path, 'r');
        if (! $handle) {
            return [];
        }

        // Read only last ~512KB to avoid loading huge files into memory
        $maxBytes = 512 * 1024;
        $fileSize = filesize($path);
        if ($fileSize > $maxBytes) {
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

                // Extract JSON context if present
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

        // Return newest first, up to limit
        return array_slice(array_reverse($entries), 0, $limit);
    }
}
