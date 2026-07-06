<?php

use App\Support\Catalog;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/collection', 'collection')->name('collection');

Route::get('/product/{slug}', function (string $slug) {
    $product = Catalog::find($slug);

    abort_unless($product, 404);

    return view('product', ['product' => $product]);
})->name('product.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
