<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Carbon;

test('order is still payable within the 10 minute window', function () {
    $order = Order::factory()->create(['created_at' => now()->subMinutes(5)]);

    $this->get(route('orders.show', $order->access_token))->assertOk();

    expect($order->fresh()->status)->toBe(OrderStatus::PendingPayment);
});

test('order auto-cancels once the 10 minute payment window has passed', function () {
    $order = Order::factory()->create(['created_at' => now()->subMinutes(11)]);

    $this->get(route('orders.show', $order->access_token))->assertOk();

    expect($order->fresh()->status)->toBe(OrderStatus::Cancelled);
});

test('expired order rejects a new payment proof upload', function () {
    $order = Order::factory()->create(['created_at' => now()->subMinutes(11)]);

    $response = $this->post(route('orders.payment-proof', $order->access_token), [
        'proof' => \Illuminate\Http\UploadedFile::fake()->image('proof.jpg'),
    ]);

    $response->assertSessionHasErrors('proof');
    expect($order->fresh()->status)->toBe(OrderStatus::Cancelled);
});

test('order already awaiting confirmation is not cancelled after 10 minutes', function () {
    $order = Order::factory()->withProof()->create(['created_at' => now()->subMinutes(20)]);

    $this->get(route('orders.show', $order->access_token))->assertOk();

    expect($order->fresh()->status)->toBe(OrderStatus::AwaitingConfirmation);
});
