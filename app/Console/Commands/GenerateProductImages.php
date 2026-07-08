<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Support\PlaceholderImageGenerator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('products:generate-images {--force : Regenerate even for products that already have an image}')]
#[Description('Generate placeholder studio photos for products that don\'t have a real image yet')]
class GenerateProductImages extends Command
{
    public function handle(): void
    {
        $products = Product::query()
            ->when(! $this->option('force'), fn ($query) => $query->whereNull('image'))
            ->get();

        if ($products->isEmpty()) {
            $this->info('Nothing to generate.');

            return;
        }

        foreach ($products as $product) {
            $path = PlaceholderImageGenerator::generate($product->slug, $product->gradient);
            $product->update(['image' => $path]);
            $this->line("Generated image for {$product->name}");
        }

        $this->info("Generated {$products->count()} image(s).");
    }
}
