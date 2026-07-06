<?php

namespace App\Support;

class Catalog
{
    public static function products(): array
    {
        return [
            [
                'slug' => 'drape-linen-coat',
                'name' => 'Drape Linen Coat',
                'price' => 'Rp 1.249.000',
                'priceValue' => 1249000,
                'rating' => 5,
                'reviews' => 12,
                'badge' => 'Bestseller',
                'gradient' => 'from-neutral-300 to-neutral-400',
                'description' => 'An oversized coat cut from heavyweight linen, built to drape rather than structure. Dropped shoulders and a relaxed silhouette make it as easy to layer as it is to wear alone.',
                'sizes' => ['S', 'M', 'L', 'XL'],
            ],
            [
                'slug' => 'oversized-linen-shirt',
                'name' => 'Oversized Linen Shirt',
                'price' => 'Rp 349.000',
                'priceValue' => 349000,
                'rating' => 4,
                'reviews' => 8,
                'badge' => 'New',
                'gradient' => 'from-sky-100 to-neutral-300',
                'description' => 'A boxy linen shirt with a soft hand-feel that only improves with wear. Cut generous through the body for effortless layering over or under anything.',
                'sizes' => ['S', 'M', 'L', 'XL'],
            ],
            [
                'slug' => 'fluid-trousers',
                'name' => 'Fluid Trousers',
                'price' => 'Rp 449.000',
                'priceValue' => 449000,
                'rating' => 5,
                'reviews' => 6,
                'badge' => null,
                'gradient' => 'from-stone-200 to-stone-400',
                'description' => 'Wide-leg trousers in a fluid, breathable weave. Sits at the natural waist with a relaxed drape through the leg for all-day movement.',
                'sizes' => ['S', 'M', 'L', 'XL'],
            ],
            [
                'slug' => 'bias-cut-slip-dress',
                'name' => 'Bias-Cut Slip Dress',
                'price' => 'Rp 529.000',
                'priceValue' => 529000,
                'rating' => 5,
                'reviews' => 9,
                'badge' => 'New',
                'gradient' => 'from-neutral-200 to-neutral-400',
                'description' => 'Cut on the bias for a fluid drape that moves with the body. Minimal detailing lets the fabric do the work, day or night.',
                'sizes' => ['XS', 'S', 'M', 'L'],
            ],
            [
                'slug' => 'boxy-tee',
                'name' => 'Boxy Tee',
                'price' => 'Rp 229.000',
                'priceValue' => 229000,
                'rating' => 4,
                'reviews' => 14,
                'badge' => null,
                'gradient' => 'from-neutral-300 to-neutral-500',
                'description' => 'A heavyweight cotton tee with a dropped shoulder and boxy fit. The everyday foundation piece, built to hold its shape wash after wash.',
                'sizes' => ['S', 'M', 'L', 'XL'],
            ],
            [
                'slug' => 'linen-wrap-top',
                'name' => 'Linen Wrap Top',
                'price' => 'Rp 389.000',
                'priceValue' => 389000,
                'rating' => 5,
                'reviews' => 7,
                'badge' => 'New',
                'gradient' => 'from-emerald-900/30 to-neutral-400',
                'description' => 'A half-zip wrap top in brushed linen-cotton, designed for layering through every season. Relaxed through the body with a raised collar.',
                'sizes' => ['S', 'M', 'L'],
            ],
        ];
    }

    public static function find(string $slug): ?array
    {
        foreach (self::products() as $product) {
            if ($product['slug'] === $slug) {
                return $product;
            }
        }

        return null;
    }
}
