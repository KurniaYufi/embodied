<?php

use App\Enums\OrderStatus;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome', [
        'newArrivals' => Product::orderBy('id')->take(5)->get(),
        'lookbook' => Product::where('is_new', true)->orderBy('id')->take(3)->get(),
        'heroImage' => Storage::disk('supabase')->url('site/hero.png'),
        'aboutImage' => Storage::disk('supabase')->url('site/about.jpg'),
    ]);
})->name('home');

Route::get('/collection', [CollectionController::class, 'index'])->name('collection');

Route::get('/product/{product}', function (Product $product) {
    return view('product', [
        'product' => $product->load('sizes'),
        'allSizes' => Size::orderBy('sort_order')->get(),
    ]);
})->name('product.show');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/account/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{token}/reviews', [ReviewController::class, 'store'])->name('orders.reviews.store');
});

Route::get('/orders/{token}', [OrderController::class, 'show'])->name('orders.show');
Route::post('/orders/{token}/payment-proof', [OrderController::class, 'uploadProof'])->name('orders.payment-proof');

Route::view('/help', 'help')->name('help');

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('dashboard', function () {
        return view('dashboard', [
            'productCount' => Product::count(),
            'categoryCount' => Category::count(),
            'outOfStockCount' => Product::where('stock', '<=', 0)->count(),
            'orderCount' => Order::count(),
            'awaitingConfirmationCount' => Order::where('status', OrderStatus::AwaitingConfirmation)->count(),
            'recentProducts' => Product::with('category')->latest()->take(5)->get(),
        ]);
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/seller.php';
