<?php

use App\Models\Order;
use App\Models\User;

test('guests are redirected to login when visiting order history', function () {
    $this->get(route('orders.index'))->assertRedirect(route('login'));
});

test('customer only sees their own orders', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $ownOrder = Order::factory()->create(['user_id' => $user->id]);
    Order::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->get(route('orders.index'));

    $response->assertOk();
    $response->assertSee($ownOrder->number);
    $response->assertViewHas('orders', fn ($orders) => $orders->total() === 1);
});

test('checkout associates the new order with the logged-in user', function () {
    $user = User::factory()->create();

    $items = json_encode([
        ['name' => 'Boxy Tee', 'priceValue' => 229000, 'gradient' => 'from-neutral-300 to-neutral-500', 'size' => 'M', 'qty' => 1],
    ]);

    $this->actingAs($user)->post(route('checkout.store'), [
        'customer_name' => 'Jane Doe',
        'customer_phone' => '081234567890',
        'shipping_address' => 'Jl. Contoh No. 1, Jakarta',
        'items' => $items,
    ]);

    $order = Order::first();

    expect($order->user_id)->toBe($user->id);
});
