<?php

namespace Database\Seeders;

use App\Enums\PaymentMethodType;
use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentMethod::updateOrCreate(
            ['type' => PaymentMethodType::Bank, 'name' => 'BCA'],
            [
                'account_name' => 'PT Embodied Studio',
                'account_number' => '1234567890',
                'is_active' => true,
                'sort_order' => 0,
            ],
        );
    }
}
