<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ExpoPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private readonly ExpoPushService $push) {}

    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:1000'],
            'type' => ['required', 'in:group,users'],
            'roles' => ['required_if:type,group', 'array'],
            'roles.*' => ['in:seller,client,driver'],
            'user_ids' => ['required_if:type,users', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $title = $request->string('title')->toString();
        $body = $request->string('body')->toString();
        $data = ['type' => 'admin_broadcast'];
        $count = 0;

        if ($request->type === 'group') {
            $users = User::whereIn('role', $request->roles)
                ->where('status', 'active')
                ->get();

            foreach ($users as $user) {
                $this->push->sendToUser($user, $title, $body, $data);
                $count++;
            }
        } else {
            $users = User::whereIn('id', $request->user_ids)->get();

            foreach ($users as $user) {
                $this->push->sendToUser($user, $title, $body, $data);
                $count++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Notification sent to {$count} user(s).",
            'count' => $count,
        ]);
    }
}
