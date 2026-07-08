<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'slug' => Str::slug($name),
            'name' => Str::title($name),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(150, 1500) * 1000,
            'gradient' => fake()->randomElement([
                'from-neutral-300 to-neutral-400',
                'from-neutral-200 to-neutral-400',
                'from-stone-200 to-stone-400',
                'from-sky-100 to-neutral-300',
            ]),
            'is_bestseller' => false,
            'is_new' => false,
            'stock' => fake()->numberBetween(0, 50),
        ];
    }
}
