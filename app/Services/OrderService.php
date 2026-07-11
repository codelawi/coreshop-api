<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(private readonly ExpoPushService $push) {}

    /**
     * @param array{
     *   address_id: int,
     *   items: array<array{product_id: int, variant_id: int|null, quantity: int}>,
     *   coupon_code?: string|null,
     *   notes?: string|null,
     *   payment_method?: string|null
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

            $feePerKm = (float) Setting::get('delivery_fee_per_km', 0.3);
            $feeMinimum = (float) Setting::get('delivery_fee_minimum', 1.0);
            $platformFeePercent = (float) Setting::get('platform_fee_percentage', 10);

            $distanceKm = $this->haversine(
                (float) $store->latitude,
                (float) $store->longitude,
                (float) $address->latitude,
                (float) $address->longitude,
            );
            $deliveryFee = round(max($feeMinimum, $feeMinimum + $distanceKm * $feePerKm), 2);
            $platformFee = round(($subtotal - $discount) * ($platformFeePercent / 100), 2);
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
                'platform_fee' => $platformFee,
                'distance_km' => round($distanceKm, 2),
                'total' => $total,
                'payment_method' => $data['payment_method'] ?? 'cash_on_delivery',
                'payment_status' => 'unpaid',
                'notes' => $data['notes'] ?? null,
                'delivery_latitude' => $address->latitude,
                'delivery_longitude' => $address->longitude,
            ]);

            $order->items()->createMany($resolvedItems);

            // Notify the seller about the new order
            $seller = $store->seller;
            if ($seller) {
                $lang = $seller->language ?? 'ar';
                $title = $lang === 'ar' ? 'طلب جديد!' : 'New Order!';
                $body = $lang === 'ar'
                    ? "لديك طلب جديد رقم #{$order->id} ينتظر موافقتك."
                    : "You have a new order #{$order->id} waiting for approval.";

                $this->push->sendToUser($seller, $title, $body, [
                    'type' => 'new_order',
                    'order_id' => $order->id,
                ]);
            }

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
