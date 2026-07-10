<?php

namespace App\Observers;

use App\Events\AdminNotificationCreated;
use App\Models\AdminNotification;
use App\Models\Setting;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        if (! Setting::get('notif_new_users', true)) {
            return;
        }

        $notification = AdminNotification::create([
            'type' => 'new_user',
            'title' => 'New User Registered',
            'body' => "{$user->name} joined as {$user->role}.",
            'data' => ['user_id' => $user->id],
        ]);

        AdminNotificationCreated::dispatch($notification);
    }
}
