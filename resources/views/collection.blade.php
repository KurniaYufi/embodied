@php $products = \App\Support\Catalog::products(); @endphp

<x-layout :title="'Collection 01 — ' . config('app.name', 'Embodied')">

    <header class="flex items-center justify-between border-b border-neutral-200 px-6 py-5 sm:px-10">
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-xs font-medium tracking-[0.25em] uppercase">
            <span aria-hidden="true">&larr;</span> Embodied
        </a>

        <h1 class="font-serif text-xl">Collection 01</h1>

        <x-cart-trigger class="relative text-neutral-900" />
    </header>

    <div class="mx-auto flex max-w-7xl flex-col gap-10 px-6 py-10 sm:px-10 lg:flex-row">

        {{-- Sidebar --}}
        <aside class="w-full shrink-0 lg:w-64">
            <div class="relative mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-neutral-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="11" cy="11" r="7" />
                    <path stroke-linecap="round" d="m21 21-4.3-4.3" />
                </svg>
                <input type="search" placeholder="Search" class="w-full border border-neutral-300 bg-transparent py-2 pr-3 pl-9 text-sm placeholder:text-neutral-400 focus:border-neutral-900 focus:outline-none">
            </div>

            <div class="mb-8 border-b border-neutral-200 pb-8">
                <x-filter-list title="Categories" :items="['All', 'New Arrivals', 'Women', 'Men', 'Accessories']" />
            </div>

            <div class="mb-8 border-b border-neutral-200 pb-8">
                <p class="mb-4 text-xs tracking-[0.2em] text-neutral-500 uppercase">Availability</p>
                <label class="flex items-center gap-2 text-sm text-neutral-700">
                    <input type="checkbox" checked class="h-4 w-4 rounded-none border-neutral-400 text-neutral-900 focus:ring-0">
                    In Stock
                </label>
            </div>

            <x-filter-list title="Price" :items="['All Prices', 'Under Rp 300.000', 'Rp 300.000 – 500.000', 'Rp 500.000 – 1.000.000', 'Rp 1.000.000+']" />
        </aside>

        {{-- Product grid --}}
        <div class="flex-1">
            <div class="mb-6 flex items-center justify-between text-sm text-neutral-500">
                <span>{{ count($products) }} items</span>
                <span>sort : <button type="button" class="text-neutral-900 underline underline-offset-4">Recent &darr;</button></span>
            </div>

            <div class="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($products as $product)
                    <x-shop-product-card
                        :name="$product['name']"
                        :price="$product['price']"
                        :price-value="$product['priceValue']"
                        :rating="$product['rating']"
                        :reviews="$product['reviews']"
                        :badge="$product['badge']"
                        :gradient="$product['gradient']"
                        :href="route('product.show', $product['slug'])"
                    />
                @endforeach
            </div>
        </div>
    </div>
</x-layout>
