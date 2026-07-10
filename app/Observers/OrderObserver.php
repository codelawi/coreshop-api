<?php

namespace App\Observers;

use App\Events\AdminNotificationCreated;
use App\Models\AdminNotification;
use App\Models\Order;
use App\Models\Setting;

class OrderObserver
{
    public function created(Order $order): void
    {
        if (! Setting::get('notif_new_orders', true)) {
            return;
        }

        $notification = AdminNotification::create([
            'type' => 'new_order',
            'title' => 'New Order',
            'body' => "Order #{$order->id} placed — total JOD ".number_format((float) $order->total, 2),
            'data' => ['order_id' => $order->id],
        ]);

        AdminNotificationCreated::dispatch($notification);
    }
}
