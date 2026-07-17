<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client' => [
                'id' => $this->client->id,
                'name' => $this->client->name,
                'email' => $this->client->email,
                'avatar' => $this->client->avatar,
            ],
            'coupon' => $this->coupon ? [
                'id' => $this->coupon->id,
                'code' => $this->coupon->code,
            ] : null,
            'status' => $this->status,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'delivery_fee' => $this->delivery_fee,
            'platform_fee' => $this->platform_fee,
            'total' => $this->total,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'cliq_reference' => $this->cliq_reference,
            'notes' => $this->notes,
            'items_count' => $this->whenLoaded('items', fn () => $this->items->count(), $this->items_count ?? 0),
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'store' => $this->whenLoaded('store', fn () => $this->store ? [
                'id' => $this->store->id,
                'name' => $this->store->name,
                'city' => $this->store->city,
                'phone' => $this->store->phone,
            ] : null),
            'seller' => $this->whenLoaded('store', fn () => $this->store?->seller ? [
                'id' => $this->store->seller->id,
                'name' => $this->store->seller->name,
                'email' => $this->store->seller->email,
            ] : null),
            'address' => $this->whenLoaded('address', fn () => $this->address ? [
                'label' => $this->address->label,
                'recipient_name' => $this->address->recipient_name,
                'phone' => $this->address->phone,
                'address_line' => $this->address->address_line,
                'city' => $this->address->city,
                'building' => $this->address->building,
                'floor' => $this->address->floor,
                'apartment' => $this->address->apartment,
                'notes' => $this->address->notes,
            ] : null),
            'driver' => $this->whenLoaded('driver', fn () => $this->driver ? [
                'id' => $this->driver->id,
                'name' => $this->driver->name,
                'phone' => $this->driver->phone,
            ] : null),
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'product_image' => $item->product_image,
                'variant_label' => $item->variant_label,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
                'weight_grams' => $item->product?->weight_grams,
            ])),
        ];
    }
}
