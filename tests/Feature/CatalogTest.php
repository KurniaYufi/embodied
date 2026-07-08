<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Size;

test('collection page lists seeded products and categories', function () {
    $category = Category::factory()->create(['name' => 'Women']);
    $product = Product::factory()->create(['category_id' => $category->id, 'name' => 'Drape Linen Coat']);

    $response = $this->get(route('collection'));

    $response->assertOk();
    $response->assertSee('Drape Linen Coat');
    $response->assertSee('Women');
});

test('product page shows details and available sizes', function () {
    $product = Product::factory()->create(['name' => 'Fluid Trousers', 'description' => 'A fluid pair of trousers.']);
    $size = Size::factory()->create(['label' => 'M']);
    $product->sizes()->attach($size);

    $response = $this->get(route('product.show', $product));

    $response->assertOk();
    $response->assertSee('Fluid Trousers');
    $response->assertSee('A fluid pair of trousers.');
    $response->assertSee('M');
});

test('product page shows all sizes and disables ones not assigned to the product', function () {
    $availableSize = Size::factory()->create(['label' => 'M', 'sort_order' => 2]);
    $unavailableSize = Size::factory()->create(['label' => 'XL', 'sort_order' => 4]);

    $product = Product::factory()->create(['name' => 'Boxy Tee']);
    $product->sizes()->attach($availableSize);

    $response = $this->get(route('product.show', $product));

    $response->assertOk();
    $response->assertSee('M');
    $response->assertSee('XL');

    $content = $response->getContent();
    preg_match('/<input[^>]*value="M"[^>]*>/', $content, $availableInput);
    preg_match('/<input[^>]*value="XL"[^>]*>/', $content, $unavailableInput);

    expect($availableInput[0] ?? '')->not->toContain('disabled');
    expect($unavailableInput[0] ?? '')->toContain('disabled');
});

test('unknown product slug returns 404', function () {
    $response = $this->get('/product/does-not-exist');

    $response->assertNotFound();
});