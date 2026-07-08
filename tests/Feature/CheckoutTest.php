<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('checkout page loads', function () {
    $this->get(route('checkout'))->assertOk();
});

test('customer can place an order from cart items', function () {
    $items = json_encode([
        ['name' => 'Fluid Trousers', 'priceValue' => 449000, 'gradient' => 'from-stone-200 to-stone-400', 'size' => 'M', 'qty' => 2],
        ['name' => 'Boxy Tee', 'priceValue' => 229000, 'gradient' => 'from-neutral-300 to-neutral-500', 'size' => 'L', 'qty' => 1],
    ]);

    $response = $this->post(route('checkout.store'), [
        'customer_name' => 'Jane Doe',
        'customer_phone' => '081234567890',
        'shipping_address' => 'Jl. Contoh No. 1, Jakarta',
        'notes' => 'Please pack carefully',
        'items' => $items,
    ]);

    $order = Order::first();

    expect($order)->not->toBeNull();
    expect($order->subtotal)->toBe(449000 * 2 + 229000);
    expect($order->items)->toHaveCount(2);
    expect($order->status)->toBe(OrderStatus::PendingPayment);

    $response->assertRedirect(route('orders.show', $order->access_token));
});

test('checkout fails with empty cart', function () {
    $response = $this->post(route('checkout.store'), [
        'customer_name' => 'Jane Doe',
        'customer_phone' => '081234567890',
        'shipping_address' => 'Jl. Contoh No. 1',
        'items' => json_encode([]),
    ]);

    $response->assertSessionHasErrors('items');
    expect(Order::count())->toBe(0);
});

test('order status page is publicly viewable by access token', function () {
    $order = Order::factory()->create();

    $this->get(route('orders.show', $order->access_token))
        ->assertOk()
        ->assertSee($order->number);
});

test('unknown order token returns 404', function () {
    $this->get(route('orders.show', 'does-not-exist'))->assertNotFound();
});

test('customer can upload a payment proof and status moves to awaiting confirmation', function () {
    Storage::fake('public');

    $order = Order::factory()->create(['status' => OrderStatus::PendingPayment]);
    $file = UploadedFile::fake()->image('proof.jpg');

    $response = $this->post(route('orders.payment-proof', $order->access_token), [
        'proof' => $file,
    ]);

    $order->refresh();

    expect($order->status)->toBe(OrderStatus::AwaitingConfirmation);
    expect($order->payment_proof_path)->not->toBeNull();
    Storage::disk('public')->assertExists($order->payment_proof_path);

    $response->assertRedirect(route('orders.show', $order->access_token));
});

test('payment proof cannot be uploaded once order is paid', function () {
    Storage::fake('public');

    $order = Order::factory()->create(['status' => OrderStatus::Paid]);
    $file = UploadedFile::fake()->image('proof.jpg');

    $response = $this->post(route('orders.payment-proof', $order->access_token), [
        'proof' => $file,
    ]);

    $response->assertSessionHasErrors('proof');
    expect($order->fresh()->payment_proof_path)->toBeNull();
});