<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Feedback::with('user:id,name,email,avatar')
            ->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $feedbacks = $query->paginate($request->integer('per_page', 50));

        return response()->json([
            'data' => $feedbacks->map(fn (Feedback $f) => [
                'id' => $f->id,
                'type' => $f->type,
                'description' => $f->description,
                'steps' => $f->steps,
                'status' => $f->status,
                'created_at' => $f->created_at->toDateTimeString(),
                'user' => $f->user ? [
                    'id' => $f->user->id,
                    'name' => $f->user->name,
                    'email' => $f->user->email,
                    'avatar' => $f->user->avatar,
                ] : null,
            ]),
            'meta' => [
                'total' => $feedbacks->total(),
                'current_page' => $feedbacks->currentPage(),
                'last_page' => $feedbacks->lastPage(),
            ],
        ]);
    }

    public function updateStatus(Request $request, Feedback $feedback): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:new,in_progress,resolved'],
        ]);

        $feedback->update(['status' => $data['status']]);

        return response()->json(['success' => true]);
    }
}
