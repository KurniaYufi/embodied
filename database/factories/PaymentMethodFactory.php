<?php

namespace Database\Factories;

use App\Enums\PaymentMethodType;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => PaymentMethodType::Bank,
            'name' => fake()->randomElement(['BCA', 'Mandiri', 'BNI', 'BRI']),
            'account_name' => 'PT Embodied Studio',
            'account_number' => fake()->numerify('##########'),
            'image_path' => null,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function qris(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PaymentMethodType::Qris,
            'name' => 'QRIS',
            'account_name' => null,
            'account_number' => null,
            'image_path' => 'payment-methods/qris-sample.jpg',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
