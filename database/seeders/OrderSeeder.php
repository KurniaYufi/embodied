<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Support\PlaceholderImageGenerator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            return;
        }

        $sampleProofPath = PlaceholderImageGenerator::generateSampleReceipt();

        $orders = [
            [
                'customer_name' => 'Siti Rahayu',
                'customer_phone' => '081234567890',
                'shipping_address' => "Jl. Kenanga No. 12\nJakarta Selatan, DKI Jakarta 12210",
                'status' => OrderStatus::PendingPayment,
                'items' => [['product' => 0, 'quantity' => 1]],
            ],
            [
                'customer_name' => 'Budi Santoso',
                'customer_phone' => '081298765432',
                'shipping_address' => "Jl. Merdeka No. 45\nBandung, Jawa Barat 40115",
                'status' => OrderStatus::AwaitingConfirmation,
                'payment_proof_path' => $sampleProofPath,
                'items' => [['product' => 1, 'quantity' => 2], ['product' => 4, 'quantity' => 1]],
            ],
            [
                'customer_name' => 'Dewi Lestari',
                'customer_phone' => '081355512345',
                'shipping_address' => "Jl. Pahlawan No. 8\nSurabaya, Jawa Timur 60174",
                'status' => OrderStatus::Paid,
                'payment_proof_path' => $sampleProofPath,
                'items' => [['product' => 3, 'quantity' => 1]],
            ],
            [
                'customer_name' => 'Andi Wijaya',
                'customer_phone' => '087811122233',
                'shipping_address' => "Jl. Diponegoro No. 21\nYogyakarta, DIY 55223",
                'status' => OrderStatus::Completed,
                'payment_proof_path' => $sampleProofPath,
                'items' => [['product' => 2, 'quantity' => 1], ['product' => 5, 'quantity' => 1]],
            ],
        ];

        foreach ($orders as $data) {
            $items = $data['items'];
            unset($data['items']);

            $subtotal = collect($items)->sum(
                fn ($item) => $products[$item['product']]->price * $item['quantity']
            );

            $order = Order::create([
                ...$data,
                'subtotal' => $subtotal,
                'payment_proof_uploaded_at' => isset($data['payment_proof_path']) ? now() : null,
            ]);

            foreach ($items as $item) {
                $product = $products[$item['product']];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'size' => $product->sizes()->first()?->label ?? 'One Size',
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'gradient' => $product->gradient,
                    'image' => $product->image_url,
                ]);
            }
        }
    }
}
