<?php

namespace App\Observers;

use App\Events\AdminNotificationCreated;
use App\Models\AdminNotification;
use App\Models\Product;
use App\Models\Setting;

class ProductObserver
{
    public function created(Product $product): void
    {
        if (! Setting::get('notif_new_products', true)) {
            return;
        }

        $notification = AdminNotification::create([
            'type' => 'new_product',
            'title' => 'New Product Listed',
            'body' => "\"{$product->name}\" was submitted for review.",
            'data' => ['product_id' => $product->id],
        ]);

        AdminNotificationCreated::dispatch($notification);
    }
}
