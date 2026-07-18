<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'in:bug,problem'],
            'description' => ['required', 'string', 'max:3000'],
            'steps' => ['nullable', 'string', 'max:2000'],
        ]);

        Log::channel('stack')->info('User feedback received', [
            'user_id' => Auth::id(),
            'type' => $data['type'],
            'description' => $data['description'],
            'steps' => $data['steps'] ?? null,
        ]);

        return response()->json(['success' => true]);
    }
}
