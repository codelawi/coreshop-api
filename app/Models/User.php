<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'avatar',
        'onboarding_completed',
        'city',
        'latitude',
        'longitude',
        'interests',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'onboarding_completed' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'interests' => 'array',
        ];
    }

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    public function store()
    {
        return $this->hasOne(Store::class, 'seller_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSeller(): bool
    {
        return $this->role === 'seller';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function defaultAddress()
    {
        return $this->addresses()->where('is_default', true)->first();
    }
}