<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use App\Support\PlaceholderImageGenerator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'category' => 'women',
                'slug' => 'drape-linen-coat',
                'name' => 'Drape Linen Coat',
                'price' => 1249000,
                'gradient' => 'from-neutral-300 to-neutral-400',
                'is_bestseller' => true,
                'is_new' => false,
                'stock' => 8,
                'description' => 'An oversized coat cut from heavyweight linen, built to drape rather than structure. Dropped shoulders and a relaxed silhouette make it as easy to layer as it is to wear alone.',
                'sizes' => ['S', 'M', 'L', 'XL'],
            ],
            [
                'category' => 'men',
                'slug' => 'oversized-linen-shirt',
                'name' => 'Oversized Linen Shirt',
                'price' => 349000,
                'gradient' => 'from-sky-100 to-neutral-300',
                'is_bestseller' => false,
                'is_new' => true,
                'stock' => 25,
                'description' => 'A boxy linen shirt with a soft hand-feel that only improves with wear. Cut generous through the body for effortless layering over or under anything.',
                'sizes' => ['S', 'M', 'L', 'XL'],
            ],
            [
                'category' => 'men',
                'slug' => 'fluid-trousers',
                'name' => 'Fluid Trousers',
                'price' => 449000,
                'gradient' => 'from-stone-200 to-stone-400',
                'is_bestseller' => false,
                'is_new' => false,
                'stock' => 18,
                'description' => 'Wide-leg trousers in a fluid, breathable weave. Sits at the natural waist with a relaxed drape through the leg for all-day movement.',
                'sizes' => ['S', 'M', 'L', 'XL'],
            ],
            [
                'category' => 'women',
                'slug' => 'bias-cut-slip-dress',
                'name' => 'Bias-Cut Slip Dress',
                'price' => 529000,
                'gradient' => 'from-neutral-200 to-neutral-400',
                'is_bestseller' => false,
                'is_new' => true,
                'stock' => 14,
                'description' => 'Cut on the bias for a fluid drape that moves with the body. Minimal detailing lets the fabric do the work, day or night.',
                'sizes' => ['XS', 'S', 'M', 'L'],
            ],
            [
                'category' => 'men',
                'slug' => 'boxy-tee',
                'name' => 'Boxy Tee',
                'price' => 229000,
                'gradient' => 'from-neutral-300 to-neutral-500',
                'is_bestseller' => false,
                'is_new' => false,
                'stock' => 40,
                'description' => 'A heavyweight cotton tee with a dropped shoulder and boxy fit. The everyday foundation piece, built to hold its shape wash after wash.',
                'sizes' => ['S', 'M', 'L', 'XL'],
            ],
            [
                'category' => 'women',
                'slug' => 'linen-wrap-top',
                'name' => 'Linen Wrap Top',
                'price' => 389000,
                'gradient' => 'from-emerald-900/30 to-neutral-400',
                'is_bestseller' => false,
                'is_new' => true,
                'stock' => 20,
                'description' => 'A half-zip wrap top in brushed linen-cotton, designed for layering through every season. Relaxed through the body with a raised collar.',
                'sizes' => ['S', 'M', 'L'],
            ],
        ];

        foreach ($products as $data) {
            $sizes = $data['sizes'];
            $categorySlug = $data['category'];
            unset($data['sizes'], $data['category']);

            $data['category_id'] = Category::where('slug', $categorySlug)->value('id');
            $data['image'] = PlaceholderImageGenerator::generate($data['slug'], $data['gradient']);

            $product = Product::updateOrCreate(['slug' => $data['slug']], $data);

            $sizeIds = Size::whereIn('label', $sizes)->pluck('id');
            $product->sizes()->sync($sizeIds);
        }
    }
}
