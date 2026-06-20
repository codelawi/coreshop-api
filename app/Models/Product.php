<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'seller_id',
        'store_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'original_price',
        'stock',
        'weight_grams',
        'images',
        'status',
        'rating',
        'reviews_count',
        'sales_count',
        'views_count',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'rating' => 'decimal:2',
            'is_featured' => 'boolean',
        ];
    }

    // Relationships
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Accessors
    public function getDiscountPercentAttribute(): ?int
    {
        if (!$this->original_price || $this->original_price <= $this->price) {
            return null;
        }
        return (int) round(100 - ($this->price / $this->original_price * 100));
    }

    // Scopes
    public function scopeApproved($q)
    {
        return $q->where('status', 'approved');
    }

    public function scopeInStock($q)
    {
        return $q->where('stock', '>', 0);
    }
}