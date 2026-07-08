<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => 'ORD-'.now()->format('Ymd').'-'.strtoupper(Str::random(4)),
            'access_token' => Str::random(40),
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->numerify('08##########'),
            'shipping_address' => fake()->address(),
            'notes' => null,
            'subtotal' => fake()->numberBetween(150, 2500) * 1000,
            'status' => OrderStatus::PendingPayment,
            'payment_proof_path' => null,
            'payment_proof_uploaded_at' => null,
        ];
    }

    public function withProof(): static
    {
        return $this->state(fn () => [
            'status' => OrderStatus::AwaitingConfirmation,
            'payment_proof_path' => 'payment-proofs/sample.jpg',
            'payment_proof_uploaded_at' => now(),
        ]);
    }
}
