<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'delivery_fee' => $this->delivery_fee,
            'distance_km' => $this->distance_km,
            'total' => $this->total,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'notes' => $this->notes,
            'store' => $this->whenLoaded('store', fn () => [
                'id' => $this->store->id,
                'name' => $this->store->name,
                'logo' => $this->store->logo,
            ]),
            'address' => $this->whenLoaded('address', fn () => $this->address ? [
                'label' => $this->address->label,
                'recipient_name' => $this->address->recipient_name,
                'address_line' => $this->address->address_line,
                'city' => $this->address->city,
            ] : null),
            'coupon' => $this->whenLoaded('coupon', fn () => $this->coupon ? [
                'code' => $this->coupon->code,
            ] : null),
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'product_image' => $item->product_image,
                'variant_label' => $item->variant_label,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
            ])->values()->all()),
            'items_count' => $this->whenLoaded('items', fn () => $this->items->count()),
            'created_at' => $this->created_at->toIso8601String(),
            'approved_at' => $this->approved_at?->toIso8601String(),
            'delivered_at' => $this->delivered_at?->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
        ];
    }
}
