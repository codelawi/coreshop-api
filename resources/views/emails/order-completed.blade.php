<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Order #{{ $order->id }} — CoreShop</title>
<style>
  body { margin: 0; padding: 0; background: #F4F4F5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #18181B; }
  .wrapper { max-width: 560px; margin: 32px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
  .header { background: #0A0A0A; padding: 28px 32px; text-align: center; }
  .header span { color: #FF4D4F; font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
  .header span em { color: #ffffff; font-style: normal; }
  .badge { display: inline-block; margin-top: 10px; background: #16A34A; color: #fff; padding: 4px 14px; border-radius: 99px; font-size: 12px; font-weight: 600; letter-spacing: 0.5px; }
  .body { padding: 28px 32px; }
  h2 { margin: 0 0 6px; font-size: 18px; font-weight: 700; color: #0A0A0A; }
  .sub { margin: 0 0 24px; font-size: 14px; color: #71717A; }
  .items { border: 1px solid #E4E4E7; border-radius: 8px; overflow: hidden; margin-bottom: 20px; }
  .item { display: flex; align-items: flex-start; padding: 12px 16px; gap: 12px; border-bottom: 1px solid #F4F4F5; }
  .item:last-child { border-bottom: none; }
  .item-img { width: 48px; height: 48px; border-radius: 6px; object-fit: cover; background: #F4F4F5; flex-shrink: 0; }
  .item-name { font-size: 14px; font-weight: 600; color: #18181B; margin: 0 0 2px; }
  .item-meta { font-size: 12px; color: #71717A; margin: 0; }
  .item-price { margin-left: auto; text-align: right; }
  .item-price strong { font-size: 14px; font-weight: 700; color: #18181B; display: block; }
  .item-price span { font-size: 11px; color: #A1A1AA; }
  .totals { background: #FAFAFA; border-radius: 8px; padding: 16px; margin-bottom: 20px; }
  .row { display: flex; justify-content: space-between; font-size: 13px; color: #52525B; margin-bottom: 6px; }
  .row:last-child { margin-bottom: 0; }
  .row.total { font-size: 15px; font-weight: 700; color: #0A0A0A; border-top: 1px solid #E4E4E7; padding-top: 10px; margin-top: 4px; }
  .row.green { color: #16A34A; }
  .row.fee { color: #EF4444; }
  .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; }
  .meta-item { background: #FAFAFA; border-radius: 8px; padding: 12px 14px; }
  .meta-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #A1A1AA; margin-bottom: 3px; }
  .meta-value { font-size: 13px; font-weight: 600; color: #18181B; }
  .footer { background: #FAFAFA; border-top: 1px solid #E4E4E7; padding: 20px 32px; text-align: center; font-size: 12px; color: #A1A1AA; }
  .footer a { color: #FF4D4F; text-decoration: none; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <div><span>Core<em>Shop</em></span></div>
    <div class="badge">✓ Order Completed</div>
  </div>

  <div class="body">
    @if($recipientType === 'client')
      <h2>Your order has been completed!</h2>
      <p class="sub">Thank you for shopping with CoreShop. Here's your receipt for Order <strong>#{{ $order->id }}</strong>.</p>
    @else
      <h2>Order #{{ $order->id }} is complete</h2>
      <p class="sub">The following order from your store has been marked as completed. Here's the breakdown.</p>
    @endif

    {{-- Items --}}
    <div class="items">
      @foreach($order->items as $item)
      <div class="item">
        @if($item->product_image)
        <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="item-img" />
        @else
        <div class="item-img"></div>
        @endif
        <div style="flex:1; min-width:0;">
          <p class="item-name">{{ $item->product_name }}</p>
          @if($item->variant_label)
          <p class="item-meta">{{ $item->variant_label }}</p>
          @endif
          <p class="item-meta">Qty: {{ $item->quantity }}</p>
        </div>
        <div class="item-price">
          <strong>JOD {{ number_format($item->total, 2) }}</strong>
          <span>JOD {{ number_format($item->unit_price, 2) }} each</span>
        </div>
      </div>
      @endforeach
    </div>

    {{-- Financials --}}
    <div class="totals">
      <div class="row"><span>Subtotal</span><span>JOD {{ number_format($order->subtotal, 2) }}</span></div>
      @if($order->discount > 0)
      <div class="row green"><span>Discount</span><span>−JOD {{ number_format($order->discount, 2) }}</span></div>
      @endif
      <div class="row"><span>Delivery Fee</span><span>JOD {{ number_format($order->delivery_fee ?? 0, 2) }}</span></div>

      @if($recipientType === 'seller')
      <div class="row fee"><span>Platform Fee ({{ $platformFeePercent }}%)</span><span>−JOD {{ number_format($order->platform_fee ?? 0, 2) }}</span></div>
      @endif

      <div class="row total">
        @if($recipientType === 'seller')
          <span>Your Net Earnings</span>
          <span>JOD {{ number_format(($order->total - ($order->delivery_fee ?? 0) - ($order->platform_fee ?? 0)), 2) }}</span>
        @else
          <span>Total Charged</span>
          <span>JOD {{ number_format($order->total, 2) }}</span>
        @endif
      </div>
    </div>

    {{-- Order meta --}}
    <div class="meta-grid">
      <div class="meta-item">
        <div class="meta-label">Order ID</div>
        <div class="meta-value">#{{ $order->id }}</div>
      </div>
      <div class="meta-item">
        <div class="meta-label">Payment</div>
        <div class="meta-value" style="text-transform:capitalize;">{{ str_replace('_', ' ', $order->payment_method ?? 'Cash on Delivery') }}</div>
      </div>
      @if($recipientType === 'client' && $order->store)
      <div class="meta-item">
        <div class="meta-label">Sold by</div>
        <div class="meta-value">{{ $order->store->name }}</div>
      </div>
      @endif
      @if($recipientType === 'client' && $order->address)
      <div class="meta-item">
        <div class="meta-label">Delivered to</div>
        <div class="meta-value">{{ $order->address->city }}, {{ $order->address->address_line }}</div>
      </div>
      @endif
      <div class="meta-item">
        <div class="meta-label">Date</div>
        <div class="meta-value">{{ $order->created_at->format('d M Y') }}</div>
      </div>
    </div>

    @if($recipientType === 'client')
    <p style="font-size:13px; color:#71717A; margin:0;">We hope you love your purchase! If you have any issues, contact us at <a href="mailto:team@coreshop.io">team@coreshop.io</a>.</p>
    @else
    <p style="font-size:13px; color:#71717A; margin:0;">Your earnings will be processed in the next payout cycle. For questions, contact <a href="mailto:team@coreshop.io">team@coreshop.io</a>.</p>
    @endif
  </div>

  <div class="footer">
    <p style="margin:0 0 4px;">© {{ date('Y') }} CoreShop. All rights reserved.</p>
    <p style="margin:0;"><a href="mailto:team@coreshop.io">team@coreshop.io</a></p>
  </div>
</div>
</body>
</html>
