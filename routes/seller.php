<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('seller')->name('seller.')->group(function () {
    Route::livewire('products', 'pages::seller.products')->name('products');
    Route::livewire('categories', 'pages::seller.categories')->name('categories');
    Route::livewire('transactions', 'pages::seller.transactions')->name('transactions');
});
