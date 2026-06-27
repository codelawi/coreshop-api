<?php

use App\Http\Controllers\Api\V1\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Api\V1\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\V1\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Api\V1\Admin\StoreController as AdminStoreController;
use App\Http\Controllers\Api\V1\Admin\UploadController as AdminUploadController;
use App\Http\Controllers\Api\V1\AnalyticsController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Client\AddressController as ClientAddressController;
use App\Http\Controllers\Api\V1\Client\CategoryController as ClientCategoryController;
use App\Http\Controllers\Api\V1\Client\ConversationController as ClientConversationController;
use App\Http\Controllers\Api\V1\Client\CouponController as ClientCouponController;
use App\Http\Controllers\Api\V1\Client\HomeController as ClientHomeController;
use App\Http\Controllers\Api\V1\Client\OrderController as ClientOrderController;
use App\Http\Controllers\Api\V1\Client\ProductController as ClientProductController;
use App\Http\Controllers\Api\V1\Client\ReviewController as ClientReviewController;
use App\Http\Controllers\Api\V1\Client\StoreController as ClientStoreController;
use App\Http\Controllers\Api\V1\Client\WishlistController as ClientWishlistController;
use App\Http\Controllers\Api\V1\CouponController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\Seller\AnalyticsController as SellerAnalyticsController;
use App\Http\Controllers\Api\V1\Seller\ConversationController as SellerConversationController;
use App\Http\Controllers\Api\V1\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Api\V1\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Api\V1\Seller\StoreController as SellerStoreController;
use App\Http\Controllers\Api\V1\Seller\UploadController as SellerUploadController;
use App\Http\Controllers\Api\V1\UploadController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Auth routes — public
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:login');
        Route::post('/google', [AuthController::class, 'google'])->middleware('throttle:login');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:login');
        Route::get('/reset-password', [AuthController::class, 'showResetForm']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
            ->middleware('signed')
            ->name('api.email.verify');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::patch('/onboarding', [AuthController::class, 'onboarding']);
            Route::post('/email/resend', [AuthController::class, 'resendVerification']);
            Route::patch('/push-token', [AuthController::class, 'savePushToken']);
            Route::patch('/profile', [AuthController::class, 'updateProfile']);
            Route::patch('/change-password', [AuthController::class, 'changePassword']);
            Route::delete('/account', [AuthController::class, 'deleteAccount']);
        });
    });

    // Public client routes (browsable without auth)
    Route::get('/home', [ClientHomeController::class, 'index']);
    Route::get('/categories', [ClientCategoryController::class, 'index']);
    Route::get('/categories/{category}', [ClientCategoryController::class, 'show']);
    Route::get('/client/products', [ClientProductController::class, 'index']);
    Route::get('/client/products/{product}', [ClientProductController::class, 'show']);
    Route::get('/stores', [ClientStoreController::class, 'index']);
    Route::get('/stores/{store}', [ClientStoreController::class, 'show']);
    Route::get('/client/stores/{store}', [ClientStoreController::class, 'show']);

    // Authenticated client routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/upload/avatar', [UploadController::class, 'avatar']);

        Route::get('/client/fees', [AdminSettingController::class, 'payment']);

        Route::get('/client/coupons/check', [ClientCouponController::class, 'check']);

        Route::post('/client/orders', [ClientOrderController::class, 'store']);
        Route::get('/client/orders', [ClientOrderController::class, 'index']);
        Route::get('/client/orders/{order}', [ClientOrderController::class, 'show']);
        Route::post('/client/orders/{order}/cancel', [ClientOrderController::class, 'cancel']);
        Route::get('/client/orders/{order}/review', [ClientReviewController::class, 'show']);
        Route::post('/client/orders/{order}/review', [ClientReviewController::class, 'store']);

        Route::get('/client/wishlist', [ClientWishlistController::class, 'index']);
        Route::get('/client/wishlist/ids', [ClientWishlistController::class, 'ids']);
        Route::post('/client/wishlist/{product}', [ClientWishlistController::class, 'toggle']);

        Route::get('/addresses', [ClientAddressController::class, 'index']);
        Route::post('/addresses', [ClientAddressController::class, 'store']);
        Route::get('/addresses/{address}', [ClientAddressController::class, 'show']);
        Route::put('/addresses/{address}', [ClientAddressController::class, 'update']);
        Route::delete('/addresses/{address}', [ClientAddressController::class, 'destroy']);
        Route::patch('/addresses/{address}/default', [ClientAddressController::class, 'setDefault']);

        Route::get('/client/conversations', [ClientConversationController::class, 'index']);
        Route::post('/client/conversations', [ClientConversationController::class, 'store']);
        Route::get('/client/conversations/{conversation}/messages', [ClientConversationController::class, 'messages']);
        Route::post('/client/conversations/{conversation}/messages', [ClientConversationController::class, 'sendMessage']);
    });

    // Seller only routes
    Route::middleware(['auth:sanctum', 'role:seller'])->prefix('seller')->group(function () {
        Route::post('/upload/image', [SellerUploadController::class, 'image']);

        Route::get('/store', [SellerStoreController::class, 'show']);
        Route::post('/store', [SellerStoreController::class, 'store']);
        Route::put('/store', [SellerStoreController::class, 'update']);
        Route::patch('/store/open', [SellerStoreController::class, 'toggleOpen']);

        Route::get('/products', [SellerProductController::class, 'index']);
        Route::post('/products', [SellerProductController::class, 'store']);
        Route::get('/products/{product}', [SellerProductController::class, 'show']);
        Route::put('/products/{product}', [SellerProductController::class, 'update']);
        Route::delete('/products/{product}', [SellerProductController::class, 'destroy']);

        Route::get('/orders', [SellerOrderController::class, 'index']);
        Route::get('/orders/{order}', [SellerOrderController::class, 'show']);
        Route::patch('/orders/{order}/status', [SellerOrderController::class, 'updateStatus']);

        Route::prefix('analytics')->group(function () {
            Route::get('/overview', [SellerAnalyticsController::class, 'overview']);
            Route::get('/revenue', [SellerAnalyticsController::class, 'revenue']);
            Route::get('/top-products', [SellerAnalyticsController::class, 'topProducts']);
        });

        Route::get('/conversations', [SellerConversationController::class, 'index']);
        Route::get('/conversations/{conversation}/messages', [SellerConversationController::class, 'messages']);
        Route::post('/conversations/{conversation}/messages', [SellerConversationController::class, 'sendMessage']);
    });

    // Admin only routes
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

        // Orders
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);

        // Products
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{product}', [ProductController::class, 'show']);
        Route::patch('/products/{product}/status', [ProductController::class, 'updateStatus']);

        // Users
        Route::get('/users', [UserController::class, 'index']);
        Route::patch('/users/{user}', [UserController::class, 'update']);
        Route::patch('/users/{user}/status', [UserController::class, 'updateStatus']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);

        // Coupons
        Route::get('/coupons', [CouponController::class, 'index']);
        Route::post('/coupons', [CouponController::class, 'store']);
        Route::put('/coupons/{coupon}', [CouponController::class, 'update']);
        Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy']);

        // Upload (admin)
        Route::post('/upload/image', [AdminUploadController::class, 'image']);

        // Banners
        Route::get('/banners', [AdminBannerController::class, 'index']);
        Route::post('/banners', [AdminBannerController::class, 'store']);
        Route::put('/banners/{banner}', [AdminBannerController::class, 'update']);
        Route::delete('/banners/{banner}', [AdminBannerController::class, 'destroy']);
        Route::patch('/banners/{banner}/toggle', [AdminBannerController::class, 'toggle']);
        Route::post('/banners/reorder', [AdminBannerController::class, 'reorder']);

        // Categories
        Route::get('/admin/categories', [AdminCategoryController::class, 'index']);
        Route::post('/admin/categories', [AdminCategoryController::class, 'store']);
        Route::put('/admin/categories/{category}', [AdminCategoryController::class, 'update']);
        Route::delete('/admin/categories/{category}', [AdminCategoryController::class, 'destroy']);

        // Stores (admin)
        Route::post('/stores', [AdminStoreController::class, 'store']);
        Route::get('/stores', [AdminStoreController::class, 'index']);
        Route::get('/stores/{store}', [AdminStoreController::class, 'show']);
        Route::get('/stores/{store}/orders', [AdminStoreController::class, 'orders']);
        Route::get('/stores/{store}/products', [AdminStoreController::class, 'products']);
        Route::post('/stores/{store}/products', [AdminStoreController::class, 'createProduct']);
        Route::patch('/stores/{store}/status', [AdminStoreController::class, 'updateStatus']);

        // Settings
        Route::get('/settings/payment', [AdminSettingController::class, 'payment']);
        Route::patch('/settings/payment', [AdminSettingController::class, 'updatePayment']);

        // Analytics
        Route::prefix('analytics')->group(function () {
            Route::get('/overview', [AnalyticsController::class, 'overview']);
            Route::get('/revenue', [AnalyticsController::class, 'revenue']);
            Route::get('/orders', [AnalyticsController::class, 'orders']);
            Route::get('/users', [AnalyticsController::class, 'users']);
            Route::get('/top-products', [AnalyticsController::class, 'topProducts']);
            Route::get('/top-sellers', [AnalyticsController::class, 'topSellers']);
        });

    });

});
