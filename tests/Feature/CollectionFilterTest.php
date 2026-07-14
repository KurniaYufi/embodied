<?php

use App\Models\Category;
use App\Models\Product;

test('search matches product names case-insensitively', function () {
    Product::factory()->create(['name' => 'Fluid Trousers', 'stock' => 10]);
    Product::factory()->create(['name' => 'Boxy Tee', 'stock' => 10]);

    $response = $this->get('/collection?search=trousers');

    $response->assertOk();
    $response->assertSee('Fluid Trousers');
    $response->assertDontSee('Boxy Tee');
});

test('category=new filters to new products only', function () {
    Product::factory()->create(['name' => 'New Jacket', 'is_new' => true, 'stock' => 10]);
    Product::factory()->create(['name' => 'Old Jacket', 'is_new' => false, 'stock' => 10]);

    $response = $this->get('/collection?category=new');

    $response->assertSee('New Jacket');
    $response->assertDontSee('Old Jacket');
});

test('category=bestseller filters to bestseller products only', function () {
    Product::factory()->create(['name' => 'Star Product', 'is_bestseller' => true, 'stock' => 10]);
    Product::factory()->create(['name' => 'Regular Product', 'is_bestseller' => false, 'stock' => 10]);

    $response = $this->get('/collection?category=bestseller');

    $response->assertSee('Star Product');
    $response->assertDontSee('Regular Product');
});

test('category slug filters by real category', function () {
    $women = Category::factory()->create(['slug' => 'women']);
    $men = Category::factory()->create(['slug' => 'men']);

    Product::factory()->create(['name' => 'Womens Dress', 'category_id' => $women->id, 'stock' => 10]);
    Product::factory()->create(['name' => 'Mens Shirt', 'category_id' => $men->id, 'stock' => 10]);

    $response = $this->get('/collection?category=women');

    $response->assertSee('Womens Dress');
    $response->assertDontSee('Mens Shirt');
});

test('price filter narrows results by range', function () {
    Product::factory()->create(['name' => 'Cheap Item', 'price' => 100000, 'stock' => 10]);
    Product::factory()->create(['name' => 'Expensive Item', 'price' => 2000000, 'stock' => 10]);

    $response = $this->get('/collection?price=under-300');

    $response->assertSee('Cheap Item');
    $response->assertDontSee('Expensive Item');
});

test('in-stock filter is on by default and hides out-of-stock products', function () {
    Product::factory()->create(['name' => 'Available Item', 'stock' => 5]);
    Product::factory()->create(['name' => 'Sold Out Item', 'stock' => 0]);

    $response = $this->get('/collection');

    $response->assertSee('Available Item');
    $response->assertDontSee('Sold Out Item');

    $response = $this->get('/collection?stock=0');

    $response->assertSee('Available Item');
    $response->assertSee('Sold Out Item');
});

test('sort orders products by price', function () {
    Product::factory()->create(['name' => 'Mid Item', 'price' => 500000, 'stock' => 10]);
    Product::factory()->create(['name' => 'Low Item', 'price' => 100000, 'stock' => 10]);
    Product::factory()->create(['name' => 'High Item', 'price' => 900000, 'stock' => 10]);

    $response = $this->get('/collection?sort=price-asc');

    $content = $response->getContent();
    $lowPos = strpos($content, 'Low Item');
    $midPos = strpos($content, 'Mid Item');
    $highPos = strpos($content, 'High Item');

    expect($lowPos)->toBeLessThan($midPos);
    expect($midPos)->toBeLessThan($highPos);
});

test('collection page shows empty state when no products match', function () {
    Product::factory()->create(['name' => 'Only Product', 'stock' => 10]);

    $response = $this->get('/collection?search=nonexistent-product-xyz');

    $response->assertOk();
    $response->assertSee('No products match your filters.');
});
