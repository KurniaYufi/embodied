@php
    $hasActiveFilters = $search !== '' || $categoryFilter !== '' || $priceFilter !== '' || ! $stockFilter || $sortFilter !== 'recent';
@endphp

<x-layout :title="'Collection — ' . config('app.name', 'Embodied')">

    <header class="flex items-center justify-between border-b border-neutral-200 px-6 py-5 sm:px-10">
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-xs font-medium tracking-[0.25em] uppercase">
            <flux:icon.arrow-left class="h-4 w-4" />
        </a>

        <h1 class="font-serif text-xl">Collection</h1>

        <x-cart-trigger class="relative text-neutral-900" />
    </header>

    <div class="mx-auto flex max-w-7xl flex-col gap-5 px-6 py-10 sm:px-10 lg:flex-row">

        {{-- Sidebar --}}
        <aside class="w-full shrink-0 lg:w-64">
            <form id="collection-filters" method="GET" action="{{ route('collection') }}">
                <input type="hidden" name="category" value="{{ $categoryFilter }}">
                <input type="hidden" name="price" value="{{ $priceFilter }}">

                <div class="relative mb-6">
                    <flux:icon.magnifying-glass class="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-neutral-400" />
                    <label for="collection-search" class="sr-only">Search products</label>
                    <input
                        id="collection-search"
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search"
                        data-live-search="400"
                        autocomplete="off"
                        class="w-full border border-neutral-300 bg-transparent py-2 pr-3 pl-9 text-sm placeholder:text-neutral-400 focus:border-neutral-900 focus:outline-none focus-visible:ring-offset-2"
                    >
                </div>

                <button
                    type="button"
                    data-filters-toggle
                    aria-expanded="false"
                    class="mb-6 flex w-full items-center justify-between border border-neutral-300 px-4 py-2 text-xs tracking-[0.15em] uppercase lg:hidden"
                >
                    <span>Filters{{ $hasActiveFilters ? ' •' : '' }}</span>
                    <flux:icon.chevron-down data-filters-chevron class="h-4 w-4 shrink-0 transition-transform duration-200" />
                </button>

                <div data-filters-panel class="hidden lg:block">
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
                </div>
            </form>

            <div data-filters-panel class="hidden lg:block">
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
            </div>
        </aside>

        {{-- Product grid --}}
        <div id="collection-results" class="flex-1 transition-opacity">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3 text-sm text-neutral-500">
                <span>{{ $products->count() }} {{ $products->count() === 1 ? 'item' : 'items' }}</span>

                <div class="flex items-center gap-3 text-xs tracking-[0.15em] text-neutral-500 uppercase">
                    <span>Sort</span>
                    <x-dropdown-select
                        name="sort"
                        form="collection-filters"
                        :selected="$sortFilter"
                        :options="[
                            'recent' => 'Recent',
                            'price-asc' => 'Price: Low to High',
                            'price-desc' => 'Price: High to Low',
                            'name-asc' => 'Name: A–Z',
                        ]"
                        aria-label="Sort products"
                    />
                </div>
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
                            :stock="$product->stock"
                            :badge="$product->badge"
                            :gradient="$product->gradient"
                            :image="$product->image_url"
                            :href="route('product.show', $product)"
                            :average-rating="$product->reviews_avg_rating ?? 0"
                            :reviews-count="$product->reviews_count ?? 0"
                        />
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layout>
