<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;

test('customer can review a product from a completed order', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id, 'status' => OrderStatus::Completed]);
    $order->items()->create([
        'product_id' => $product->id,
        'product_name' => $product->name,
        'price' => $product->price,
        'quantity' => 1,
        'gradient' => $product->gradient,
    ]);

    $response = $this->actingAs($user)->post(route('orders.reviews.store', $order->access_token), [
        'product_id' => $product->id,
        'rating' => 4,
    ]);

    $response->assertRedirect(route('orders.show', $order->access_token));

    $this->assertDatabaseHas('reviews', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'order_id' => $order->id,
        'rating' => 4,
    ]);
});

test('submitting a review twice updates it instead of duplicating', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id, 'status' => OrderStatus::Completed]);
    $order->items()->create([
        'product_id' => $product->id,
        'product_name' => $product->name,
        'price' => $product->price,
        'quantity' => 1,
        'gradient' => $product->gradient,
    ]);

    $this->actingAs($user)->post(route('orders.reviews.store', $order->access_token), [
        'product_id' => $product->id,
        'rating' => 3,
    ]);

    $this->actingAs($user)->post(route('orders.reviews.store', $order->access_token), [
        'product_id' => $product->id,
        'rating' => 5,
    ]);

    expect(Review::where('user_id', $user->id)->where('product_id', $product->id)->count())->toBe(1);
    $this->assertDatabaseHas('reviews', ['user_id' => $user->id, 'product_id' => $product->id, 'rating' => 5]);
});

test('rating must be between 1 and 5', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id, 'status' => OrderStatus::Completed]);
    $order->items()->create([
        'product_id' => $product->id,
        'product_name' => $product->name,
        'price' => $product->price,
        'quantity' => 1,
        'gradient' => $product->gradient,
    ]);

    $response = $this->actingAs($user)->post(route('orders.reviews.store', $order->access_token), [
        'product_id' => $product->id,
        'rating' => 6,
    ]);

    $response->assertSessionHasErrors('rating');
    $this->assertDatabaseMissing('reviews', ['user_id' => $user->id, 'product_id' => $product->id]);
});

test('order must be completed before it can be reviewed', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id, 'status' => OrderStatus::Paid]);
    $order->items()->create([
        'product_id' => $product->id,
        'product_name' => $product->name,
        'price' => $product->price,
        'quantity' => 1,
        'gradient' => $product->gradient,
    ]);

    $this->actingAs($user)->post(route('orders.reviews.store', $order->access_token), [
        'product_id' => $product->id,
        'rating' => 5,
    ])->assertForbidden();

    $this->assertDatabaseMissing('reviews', ['user_id' => $user->id, 'product_id' => $product->id]);
});

test('only the order owner can review it', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::factory()->create();
    $order = Order::factory()->create(['user_id' => $owner->id, 'status' => OrderStatus::Completed]);
    $order->items()->create([
        'product_id' => $product->id,
        'product_name' => $product->name,
        'price' => $product->price,
        'quantity' => 1,
        'gradient' => $product->gradient,
    ]);

    $this->actingAs($otherUser)->post(route('orders.reviews.store', $order->access_token), [
        'product_id' => $product->id,
        'rating' => 5,
    ])->assertForbidden();
});

test('cannot review a product not purchased in this order', function () {
    $user = User::factory()->create();
    $purchasedProduct = Product::factory()->create();
    $otherProduct = Product::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id, 'status' => OrderStatus::Completed]);
    $order->items()->create([
        'product_id' => $purchasedProduct->id,
        'product_name' => $purchasedProduct->name,
        'price' => $purchasedProduct->price,
        'quantity' => 1,
        'gradient' => $purchasedProduct->gradient,
    ]);

    $this->actingAs($user)->post(route('orders.reviews.store', $order->access_token), [
        'product_id' => $otherProduct->id,
        'rating' => 5,
    ])->assertForbidden();
});

test('product card and detail page show the average rating', function () {
    $product = Product::factory()->create(['stock' => 10]);
    Review::factory()->create(['product_id' => $product->id, 'rating' => 4]);
    Review::factory()->create(['product_id' => $product->id, 'rating' => 2]);

    $collectionResponse = $this->get(route('collection'));
    $collectionResponse->assertOk();
    $collectionResponse->assertSee('3.0 (2)');

    $productResponse = $this->get(route('product.show', $product));
    $productResponse->assertOk();
    $productResponse->assertSee('3.0 (2)');
});
