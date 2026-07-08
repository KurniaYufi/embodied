@php
    $hasActiveFilters = $search !== '' || $categoryFilter !== '' || $priceFilter !== '' || ! $stockFilter || $sortFilter !== 'recent';
@endphp

<x-layout :title="'Collection 01 — ' . config('app.name', 'Embodied')">

    <header class="flex items-center justify-between border-b border-neutral-200 px-6 py-5 sm:px-10">
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-xs font-medium tracking-[0.25em] uppercase">
            <flux:icon.arrow-left class="h-4 w-4" /> Embodied
        </a>

        <h1 class="font-serif text-xl">Collection 01</h1>

        <x-cart-trigger class="relative text-neutral-900" />
    </header>

    <div class="mx-auto flex max-w-7xl flex-col gap-10 px-6 py-10 sm:px-10 lg:flex-row">

        {{-- Sidebar --}}
        <aside class="w-full shrink-0 lg:w-64">
            <form id="collection-filters" method="GET" action="{{ route('collection') }}">
                <input type="hidden" name="category" value="{{ $categoryFilter }}">
                <input type="hidden" name="price" value="{{ $priceFilter }}">

                <div class="relative mb-8">
                    <flux:icon.magnifying-glass class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-neutral-400" />
                    <label for="collection-search" class="sr-only">Search products</label>
                    <input
                        id="collection-search"
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search"
                        class="w-full border border-neutral-300 bg-transparent py-2 pr-3 pl-9 text-sm placeholder:text-neutral-400 focus:border-neutral-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2"
                    >
                </div>

                <div class="mb-8 border-b border-neutral-200 pb-8">
                    <p class="mb-4 text-xs tracking-[0.2em] text-neutral-500 uppercase">Availability</p>
                    <label class="flex items-center gap-2 text-sm text-neutral-700">
                        <input type="hidden" name="stock" value="0">
                        <input
                            type="checkbox"
                            name="stock"
                            value="1"
                            @checked($stockFilter)
                            onchange="this.form.submit()"
                            class="h-4 w-4 rounded-none border-neutral-400 text-neutral-900 focus:ring-0"
                        >
                        In Stock Only
                    </label>
                </div>
            </form>

            <div class="mb-8 border-b border-neutral-200 pb-8">
                <x-filter-list
                    title="Categories"
                    param="category"
                    :active="$categoryFilter"
                    :items="collect(['All' => '', 'New Arrivals' => 'new', 'Bestsellers' => 'bestseller'])->merge($categories->pluck('slug', 'name'))->all()"
                />
            </div>

            <x-filter-list
                title="Price"
                param="price"
                :active="$priceFilter"
                :items="[
                    'All Prices' => '',
                    'Under Rp 300.000' => 'under-300',
                    'Rp 300.000 – 500.000' => '300-500',
                    'Rp 500.000 – 1.000.000' => '500-1000',
                    'Rp 1.000.000+' => '1000-plus',
                ]"
            />

            @if ($hasActiveFilters)
                <a href="{{ route('collection') }}" class="mt-8 inline-block text-xs tracking-[0.15em] text-neutral-500 uppercase underline underline-offset-4 hover:text-neutral-900">
                    Clear all filters
                </a>
            @endif
        </aside>

        {{-- Product grid --}}
        <div class="flex-1">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3 text-sm text-neutral-500">
                <span>{{ $products->count() }} {{ $products->count() === 1 ? 'item' : 'items' }}</span>

                <label class="flex items-center gap-2">
                    <span>sort :</span>
                    <select
                        name="sort"
                        form="collection-filters"
                        onchange="this.form.submit()"
                        aria-label="Sort products"
                        class="border-0 bg-transparent text-neutral-900 underline underline-offset-4 focus:outline-none"
                    >
                        <option value="recent" @selected($sortFilter === 'recent')>Recent</option>
                        <option value="price-asc" @selected($sortFilter === 'price-asc')>Price: Low to High</option>
                        <option value="price-desc" @selected($sortFilter === 'price-desc')>Price: High to Low</option>
                        <option value="name-asc" @selected($sortFilter === 'name-asc')>Name: A–Z</option>
                    </select>
                </label>
            </div>

            @if ($products->isEmpty())
                <div class="border border-neutral-200 bg-cream p-12 text-center">
                    <p class="mb-3 text-sm text-neutral-600">No products match your filters.</p>
                    <a href="{{ route('collection') }}" class="text-xs tracking-[0.15em] text-neutral-900 uppercase underline underline-offset-4">Clear filters</a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($products as $product)
                        <x-shop-product-card
                            :name="$product->name"
                            :price="$product->formatted_price"
                            :price-value="$product->price"
                            :stock="$product->stock"
                            :badge="$product->badge"
                            :gradient="$product->gradient"
                            :image="$product->image_url"
                            :href="route('product.show', $product)"
                        />
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layout>
