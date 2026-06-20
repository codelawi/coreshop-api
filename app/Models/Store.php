<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'seller_id',
        'name',
        'slug',
        'logo',
        'banner',
        'description',
        'phone',
        'address',
        'city',
        'latitude',
        'longitude',
        'delivery_radius_km',
        'status',
        'is_open',
        'rating',
        'reviews_count',
        'sales_count',
        'working_hours',
    ];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'rating' => 'decimal:2',
            'working_hours' => 'array',
        ];
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }
}