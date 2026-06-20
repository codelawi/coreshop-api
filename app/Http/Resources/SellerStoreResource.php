<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerStoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'logo' => $this->logo,
            'banner' => $this->banner,
            'description' => $this->description,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'delivery_radius_km' => $this->delivery_radius_km,
            'status' => $this->status,
            'is_open' => $this->is_open,
            'rating' => $this->rating,
            'reviews_count' => $this->reviews_count,
            'sales_count' => $this->sales_count,
            'working_hours' => $this->working_hours,
            'products_count' => $this->whenCounted('products'),
            'pending_orders_count' => $this->whenCounted('pendingOrders'),
            'created_at' => $this->created_at->toDateString(),
        ];
    }
}
