<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Events\AdminNotificationCreated;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'in:bug,problem'],
            'description' => ['required', 'string', 'max:3000'],
            'steps' => ['nullable', 'string', 'max:2000'],
        ]);

        $feedback = Feedback::create([
            'user_id' => Auth::id(),
            'type' => $data['type'],
            'description' => $data['description'],
            'steps' => $data['steps'] ?? null,
            'status' => 'new',
        ]);

        $user = Auth::user();
        $typeLabel = $feedback->type === 'bug' ? 'Bug Report' : 'Problem Report';

        $notification = AdminNotification::create([
            'type' => 'new_feedback',
            'title' => "New {$typeLabel}",
            'body' => "{$user->name} submitted a {$feedback->type} report.",
            'data' => ['feedback_id' => $feedback->id],
        ]);

        event(new AdminNotificationCreated($notification));

        return response()->json(['success' => true]);
    }
}
