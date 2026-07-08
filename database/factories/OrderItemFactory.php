<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => null,
            'product_name' => fake()->words(3, true),
            'size' => fake()->randomElement(['S', 'M', 'L', 'XL']),
            'price' => fake()->numberBetween(150, 900) * 1000,
            'quantity' => fake()->numberBetween(1, 3),
            'gradient' => 'from-neutral-300 to-neutral-400',
        ];
    }
}
