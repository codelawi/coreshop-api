<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * @param array{
     *   address_id: int,
     *   items: array<array{product_id: int, variant_id: int|null, quantity: int}>,
     *   coupon_code?: string|null,
     *   notes?: string|null
     * } $data
     */
    public function place(int $clientId, array $data): Order
    {
        return DB::transaction(function () use ($clientId, $data) {
            $address = Address::where('user_id', $clientId)
                ->findOrFail($data['address_id']);

            $storeId = null;
            $resolvedItems = [];

            foreach ($data['items'] as $item) {
                $product = Product::with(['productImages'])
                    ->where('status', 'approved')
                    ->findOrFail($item['product_id']);

                if ($storeId === null) {
                    $storeId = $product->store_id;
                } elseif ($storeId !== $product->store_id) {
                    throw ValidationException::withMessages([
                        'items' => ['All items must be from the same store.'],
                    ]);
                }

                $unitPrice = (float) $product->price;
                $variantLabel = null;

                if (! empty($item['variant_id'])) {
                    $variant = ProductVariant::where('product_id', $product->id)
                        ->where('is_active', true)
                        ->findOrFail($item['variant_id']);

                    if ($variant->stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'items' => ["Not enough stock for {$product->name}."],
                        ]);
                    }

                    $unitPrice += (float) $variant->price_adjustment;
                    $parts = array_filter([$variant->size, $variant->color]);
                    $variantLabel = implode(' / ', $parts) ?: null;
                } else {
                    if ($product->stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'items' => ["Not enough stock for {$product->name}."],
                        ]);
                    }
                }

                $resolvedItems[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'product_name' => $product->name,
                    'product_image' => $product->productImages->where('is_primary', true)->first()?->url
                        ?? $product->productImages->first()?->url,
                    'variant_label' => $variantLabel,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total' => round($unitPrice * $item['quantity'], 2),
                ];
            }

            $store = Store::findOrFail($storeId);
            $subtotal = round(collect($resolvedItems)->sum('total'), 2);

            $discount = 0.0;
            $couponId = null;

            if (! empty($data['coupon_code'])) {
                $coupon = Coupon::where('code', $data['coupon_code'])->first();

                if (! $coupon || ! $coupon->isValid()) {
                    throw ValidationException::withMessages([
                        'coupon_code' => ['Invalid or expired coupon code.'],
                    ]);
                }

                if ($subtotal < (float) $coupon->min_order_amount) {
                    throw ValidationException::withMessages([
                        'coupon_code' => ["Minimum order of JOD {$coupon->min_order_amount} required for this coupon."],
                    ]);
                }

                $couponId = $coupon->id;
                $discount = $coupon->type === 'percentage'
                    ? round($subtotal * ((float) $coupon->value / 100), 2)
                    : min((float) $coupon->value, $subtotal);
                $coupon->increment('used_count');
            }

            $distanceKm = $this->haversine(
                (float) $store->latitude,
                (float) $store->longitude,
                (float) $address->latitude,
                (float) $address->longitude,
            );
            $deliveryFee = round(max(1.0, 1.0 + $distanceKm * 0.3), 2);
            $total = round($subtotal - $discount + $deliveryFee, 2);

            $order = Order::create([
                'client_id' => $clientId,
                'store_id' => $storeId,
                'address_id' => $address->id,
                'coupon_id' => $couponId,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'delivery_fee' => $deliveryFee,
                'distance_km' => round($distanceKm, 2),
                'total' => $total,
                'payment_method' => 'cash_on_delivery',
                'payment_status' => 'unpaid',
                'notes' => $data['notes'] ?? null,
                'delivery_latitude' => $address->latitude,
                'delivery_longitude' => $address->longitude,
            ]);

            $order->items()->createMany($resolvedItems);

            return $order->load(['store', 'address', 'items', 'coupon']);
        });
    }

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $R * 2 * asin(sqrt($a));
    }
}
