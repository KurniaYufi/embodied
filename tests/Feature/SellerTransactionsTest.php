<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('seller can view the transactions list', function () {
    $order = Order::factory()->create(['customer_name' => 'Jane Doe']);

    Livewire::test('pages::seller.transactions')
        ->assertSee($order->number)
        ->assertSee('Jane Doe');
});

test('seller can filter transactions by status', function () {
    Order::factory()->create(['status' => OrderStatus::Paid, 'customer_name' => 'Paid Customer']);
    Order::factory()->create(['status' => OrderStatus::PendingPayment, 'customer_name' => 'Pending Customer']);

    Livewire::test('pages::seller.transactions')
        ->set('statusFilter', OrderStatus::Paid->value)
        ->assertSee('Paid Customer')
        ->assertDontSee('Pending Customer');
});

test('seller can update an order status', function () {
    $order = Order::factory()->create(['status' => OrderStatus::AwaitingConfirmation]);

    Livewire::test('pages::seller.transactions')
        ->call('showOrder', $order->id)
        ->set('newStatus', OrderStatus::Paid->value)
        ->call('updateStatus');

    expect($order->fresh()->status)->toBe(OrderStatus::Paid);
});

test('guests cannot access transactions', function () {
    auth()->logout();

    $this->get(route('seller.transactions'))->assertRedirect(route('login'));
});