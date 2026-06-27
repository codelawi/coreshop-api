# CoreShop API

Laravel 13 REST API powering the CoreShop marketplace. Serves three clients: the mobile app (Expo), the admin dashboard (React), and future driver app.

---

## Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 (PHP 8.5) |
| Auth | Laravel Sanctum (token-based) |
| Database | MySQL via DBngin |
| Push Notifications | Expo Push API |
| Testing | Pest v4 + PHPUnit v12 |
| Code Style | Laravel Pint |

---

## Local Setup

```bash
# Requires DBngin running MySQL on port 3306
# Create database named: coreshop

composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve --host=0.0.0.0
# API available at http://localhost:8000
```

### Seeded Accounts

| Role | Email | Password |
|---|---|---|
| Admin | admin@coreshop.com | password123 |
| Seller 1–5 | seller1@coreshop.com … seller5@coreshop.com | password123 |

The seeder creates a full marketplace: category tree, 5 stores in Amman, 8 products per store with images and variants, 3 banners.

---

## Architecture

```
app/
├── Http/
│   ├── Controllers/Api/V1/
│   │   ├── Admin/          # Admin-only endpoints
│   │   ├── Client/         # Authenticated client endpoints
│   │   ├── Seller/         # Seller-only endpoints
│   │   ├── AuthController.php
│   │   ├── AnalyticsController.php
│   │   └── ...
│   ├── Middleware/
│   │   └── RoleMiddleware.php    # Guards admin/seller/driver routes
│   ├── Requests/           # Form request validation
│   └── Resources/          # Eloquent API Resources
├── Models/                 # Eloquent models
├── Services/
│   ├── ExpoPushService.php # Push notifications via Expo Push API
│   └── OrderService.php    # Order lifecycle business logic
└── Notifications/
    └── Auth/EmailVerificationNotification.php
```

**Conventions:**
- Thin controllers — all business logic lives in Services
- Eloquent only (no raw SQL except `selectRaw` for aggregates and Haversine)
- All responses follow the shape: `{ success, message, data, meta }`
- Soft deletes on users and products

---

## Database Schema

### `users`
| Column | Type | Notes |
|---|---|---|
| id | bigint | |
| name | string | |
| email | string | unique |
| password | string | bcrypt hashed |
| role | enum | admin / seller / client / driver |
| status | enum | active / suspended |
| avatar | text | URL (Dicebear or uploaded) |
| phone | string | nullable |
| city | string | nullable |
| lat / lng | decimal | nullable — set during onboarding |
| interests | json | array of category IDs |
| onboarding_completed | boolean | |
| expo_push_token | string | nullable — updated by mobile app |
| email_verified_at | timestamp | nullable |
| deleted_at | timestamp | soft delete |

### `stores`
| Column | Type | Notes |
|---|---|---|
| id | bigint | |
| seller_id | foreignId → users | |
| name / slug | string | |
| logo / banner | string | URLs |
| description | text | nullable |
| phone / address / city | string | |
| lat / lng | decimal | store GPS location |
| delivery_radius_km | decimal | service radius |
| status | enum | pending / active / suspended / closed |
| is_open | boolean | seller can toggle open/closed |
| rating / reviews_count / sales_count | decimal/int | denormalised counters |
| working_hours | json | nullable |

### `categories`
| Column | Type | Notes |
|---|---|---|
| id | bigint | |
| parent_id | foreignId | nullable — tree structure |
| name / name_ar | string | bilingual |
| slug / image / icon | string | |
| sort_order | int | |
| is_active | boolean | |

### `products`
| Column | Type | Notes |
|---|---|---|
| id | bigint | |
| seller_id / store_id / category_id | foreignId | |
| name / slug / description | string/text | |
| price / original_price | decimal | original_price nullable for sale badge |
| stock / weight_grams | int | |
| status | enum | pending_review / approved / flagged / removed |
| rating / reviews_count / sales_count / views_count | | denormalised |
| is_featured | boolean | |
| deleted_at | timestamp | soft delete |

### `product_images`
| Column | Type | Notes |
|---|---|---|
| product_id | foreignId | |
| url | string | |
| sort_order | int | |
| is_primary | boolean | |

### `product_variants`
| Column | Type | Notes |
|---|---|---|
| product_id | foreignId | |
| size / color / color_hex | string | nullable |
| description | string | nullable |
| image_url | string | nullable |
| sku | string | nullable |
| price_adjustment | decimal | added to base price |
| stock | int | |
| is_active | boolean | |

### `orders`
| Column | Type | Notes |
|---|---|---|
| id | bigint | |
| client_id / store_id / address_id | foreignId | |
| driver_id / coupon_id | foreignId | nullable |
| status | enum | pending / approved / preparing / ready_for_pickup / assigned / out_for_delivery / delivered / completed / cancelled / refunded |
| subtotal / discount / delivery_fee / platform_fee / total | decimal | |
| distance_km | decimal | Haversine distance at time of order |
| payment_method / payment_status | enum | |
| delivery_lat / delivery_lng | decimal | snapshot of address at order time |
| Timestamps | | approved_at, preparing_at, ready_at, assigned_at, out_for_delivery_at, delivered_at, completed_at, cancelled_at |

### `order_items`
| Column | Type | Notes |
|---|---|---|
| order_id | foreignId | |
| product_id / product_variant_id | foreignId | nullable |
| product_name / product_image / variant_label | string | snapshot at order time |
| quantity / unit_price / total | int/decimal | |

### `addresses`
| Column | Type | Notes |
|---|---|---|
| user_id | foreignId | |
| label / recipient_name / phone | string | |
| address_line / building / floor / apartment / city | string | nullable |
| lat / lng | decimal | |
| notes | text | nullable |
| is_default | boolean | |

### `conversations`
| Column | Type | Notes |
|---|---|---|
| client_id / seller_id | foreignId → users | |
| product_id | foreignId | nullable — started from a product |
| last_message_at | timestamp | for sorting |

### `messages`
| Column | Type | Notes |
|---|---|---|
| conversation_id | foreignId | |
| sender_id | foreignId → users | |
| body | text | |
| type | enum | text / image |
| read_at | timestamp | nullable |

### `user_notifications`
| Column | Type | Notes |
|---|---|---|
| user_id | foreignId | |
| type | string | system / order_status / new_message / etc. |
| title / body | string | |
| data | json | nullable — extra payload |
| read_at | timestamp | nullable |

### `reviews`
| Column | Type | Notes |
|---|---|---|
| user_id / product_id / store_id / order_id | foreignId | product_id and store_id nullable |
| rating | int | 1–5 |
| comment | text | nullable |
| images | json | nullable |

### `banners`
| Column | Type | Notes |
|---|---|---|
| title / subtitle | string | |
| image | string | URL |
| link_type / link_value | string | e.g. store / product |
| sort_order | int | |
| is_active | boolean | |
| starts_at / ends_at | timestamp | nullable |

### `coupons`
| Column | Type | Notes |
|---|---|---|
| code | string | unique |
| type | enum | percentage / fixed |
| value | decimal | |
| min_order_amount | decimal | nullable |
| max_uses / used_count | int | |
| expires_at | timestamp | nullable |
| is_active | boolean | |

### `settings`
| Column | Type | Notes |
|---|---|---|
| key | string | unique |
| value | json | |

---

## API Routes (107 total)

All routes are prefixed with `/api/v1`.

### Auth — Public

| Method | Route | Description |
|---|---|---|
| POST | `/auth/login` | Login (rate limited 5/min) |
| POST | `/auth/register` | Register |
| POST | `/auth/google` | Google OAuth sign-in |
| POST | `/auth/forgot-password` | Send reset email |
| GET | `/auth/reset-password` | Show reset form |
| POST | `/auth/reset-password` | Set new password |
| GET | `/auth/email/verify/{id}/{hash}` | Verify email (signed URL) |

### Auth — Authenticated

| Method | Route | Description |
|---|---|---|
| GET | `/auth/me` | Get current user |
| POST | `/auth/logout` | Revoke token |
| PATCH | `/auth/onboarding` | Complete onboarding step |
| POST | `/auth/email/resend` | Resend verification email |
| PATCH | `/auth/push-token` | Save Expo push token |
| PATCH | `/auth/profile` | Update name/phone/avatar |
| PATCH | `/auth/change-password` | Change password |
| DELETE | `/auth/account` | Delete account |

### Public Client

| Method | Route | Description |
|---|---|---|
| GET | `/home` | Banners, categories, flash deals, trending, featured, top stores |
| GET | `/categories` | Category tree |
| GET | `/categories/{id}` | Single category with children |
| GET | `/client/products` | Product list with filters/sort |
| GET | `/client/products/{id}` | Product detail with images, variants, reviews |
| GET | `/stores` | Store list (supports lat/lng for Haversine distance) |
| GET | `/stores/{id}` | Store profile |
| GET | `/client/stores/{id}` | Store profile (alias) |

### Client — Authenticated

| Method | Route | Description |
|---|---|---|
| POST | `/upload/avatar` | Upload profile photo |
| GET | `/client/fees` | Platform fee settings |
| GET | `/client/coupons/check` | Validate coupon code |
| POST | `/client/orders` | Place an order |
| GET | `/client/orders` | My order list |
| GET | `/client/orders/{id}` | Order detail |
| POST | `/client/orders/{id}/cancel` | Cancel order |
| GET | `/client/orders/{id}/review` | Get existing review for order |
| POST | `/client/orders/{id}/review` | Submit review |
| GET | `/client/wishlist` | Wishlist items |
| GET | `/client/wishlist/ids` | Wishlist product IDs only |
| POST | `/client/wishlist/{product}` | Toggle wishlist |
| GET | `/addresses` | Saved addresses |
| POST | `/addresses` | Create address |
| GET | `/addresses/{id}` | Single address |
| PUT | `/addresses/{id}` | Update address |
| DELETE | `/addresses/{id}` | Delete address |
| PATCH | `/addresses/{id}/default` | Set as default |
| GET | `/client/notifications` | Latest 100 notifications |
| GET | `/client/notifications/unread-count` | Unread count badge |
| PATCH | `/client/notifications/read-all` | Mark all read |
| PATCH | `/client/notifications/{id}` | Mark one read |
| GET | `/client/conversations` | Conversation list |
| POST | `/client/conversations` | Start conversation |
| GET | `/client/conversations/{id}/messages` | Message history |
| POST | `/client/conversations/{id}/messages` | Send message |

### Seller — Authenticated + `role:seller`

| Method | Route | Description |
|---|---|---|
| POST | `/seller/upload/image` | Upload product/store image |
| GET | `/seller/store` | Get own store |
| POST | `/seller/store` | Create store (setup wizard) |
| PUT | `/seller/store` | Update store |
| PATCH | `/seller/store/open` | Toggle open/closed |
| GET | `/seller/products` | Own product list |
| POST | `/seller/products` | Create product |
| GET | `/seller/products/{id}` | Product detail |
| PUT | `/seller/products/{id}` | Update product |
| DELETE | `/seller/products/{id}` | Delete product |
| GET | `/seller/orders` | Incoming orders |
| GET | `/seller/orders/{id}` | Order detail |
| PATCH | `/seller/orders/{id}/status` | Update order status |
| GET | `/seller/analytics/overview` | Revenue, orders, customers summary |
| GET | `/seller/analytics/revenue` | Revenue chart data |
| GET | `/seller/analytics/top-products` | Best selling products |
| GET | `/seller/conversations` | Conversations with clients |
| GET | `/seller/conversations/{id}/messages` | Message history |
| POST | `/seller/conversations/{id}/messages` | Reply to client |

### Admin — Authenticated + `role:admin`

| Method | Route | Description |
|---|---|---|
| GET/POST | `/orders`, `/orders/{id}` | All orders |
| PATCH | `/orders/{id}/status` | Update any order status |
| GET | `/products`, `/products/{id}` | All products |
| PATCH | `/products/{id}/status` | Approve / flag / remove product |
| GET | `/users` | All users |
| PATCH | `/users/{id}` | Update user |
| PATCH | `/users/{id}/status` | Ban / activate user |
| DELETE | `/users/{id}` | Delete user |
| GET/POST/PUT/DELETE | `/coupons`, `/coupons/{id}` | Coupon management |
| POST | `/upload/image` | Upload image |
| GET/POST/PUT/DELETE | `/banners`, `/banners/{id}` | Banner management |
| POST | `/banners/reorder` | Reorder banners |
| PATCH | `/banners/{id}/toggle` | Show/hide banner |
| GET/POST/PUT/DELETE | `/admin/categories`, `/admin/categories/{id}` | Category management |
| GET/POST | `/stores`, `/stores/{id}` | Store management |
| GET | `/stores/{id}/orders` | Store's orders |
| GET/POST | `/stores/{id}/products` | Store's products |
| PATCH | `/stores/{id}/status` | Approve / suspend store |
| GET | `/settings/payment` | Platform fee settings |
| PATCH | `/settings/payment` | Update platform fee |
| GET | `/analytics/overview` | Platform-wide overview |
| GET | `/analytics/revenue` | Revenue over time |
| GET | `/analytics/orders` | Orders over time |
| GET | `/analytics/users` | User growth |
| GET | `/analytics/top-products` | Top selling products |
| GET | `/analytics/top-sellers` | Top selling stores |

---

## Services

### `ExpoPushService`

Handles all push notifications. Every notification is saved to `user_notifications` before sending, so users have a persistent inbox regardless of whether they received the push.

```php
// Send to a single user (saves to DB + sends push if token exists)
$pushService->sendToUser($user, 'Title', 'Body', ['type' => 'order_status', 'order_id' => 1]);

// Send to a raw token (push only, no DB record)
$pushService->send($token, 'Title', 'Body', $data);

// Send to multiple tokens at once
$pushService->sendBatch($tokens, 'Title', 'Body', $data);
```

Payload sent to Expo Push API:
```json
{
  "to": "ExponentPushToken[...]",
  "title": "...",
  "body": "...",
  "data": { "type": "order_status", "order_id": 123 },
  "sound": "default",
  "priority": "high",
  "channelId": "coreshop_v2"
}
```

### `OrderService`

Encapsulates order lifecycle logic: status transitions, delivery fee calculation (Haversine), coupon application, platform fee deduction, stock decrement on placement, and push notifications on each status change.

---

## Middleware

### `RoleMiddleware`

Applied as `role:seller`, `role:admin`, etc. Checks `auth()->user()->role` and returns 403 if the role doesn't match.

---

## Rate Limiting

| Route group | Limit |
|---|---|
| `POST /auth/login` | 5 requests/minute per email+IP |
| `POST /auth/register` | 5 requests/minute per IP |

---

## Running Tests

```bash
php artisan test --compact
```
