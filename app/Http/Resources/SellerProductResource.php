<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerProductResource extends JsonResource
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
            'description' => $this->description,
            'price' => $this->price,
            'original_price' => $this->original_price,
            'stock' => $this->stock,
            'weight_grams' => $this->weight_grams,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            'images' => $this->whenLoaded('productImages', fn () => $this->productImages->map(fn ($img) => [
                'id' => $img->id,
                'url' => $img->url,
                'sort_order' => $img->sort_order,
                'is_primary' => $img->is_primary,
            ])),
            'variants' => $this->whenLoaded('variants', fn () => $this->variants->map(fn ($v) => [
                'id' => $v->id,
                'size' => $v->size,
                'color' => $v->color,
                'color_hex' => $v->color_hex,
                'image_url' => $v->image_url,
                'sku' => $v->sku,
                'price_adjustment' => $v->price_adjustment,
                'stock' => $v->stock,
                'is_active' => $v->is_active,
            ])),
            'rating' => $this->rating,
            'reviews_count' => $this->reviews_count,
            'sales_count' => $this->sales_count,
            'views_count' => $this->views_count,
            'created_at' => $this->created_at->toDateString(),
        ];
    }
}
