<x-layout :title="config('app.name', 'Embodied') . ' — Wear What You Are'">

    {{-- Hero --}}
    <section class="relative h-screen min-h-160 overflow-hidden bg-neutral-900">
        <img src="{{ $heroImage }}" alt="" class="absolute inset-0 h-full w-full object-cover object-[70%_20%]">
        <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/30 to-black/50"></div>

        <header class="absolute inset-x-0 top-0 z-20 flex items-center justify-between px-6 py-6 text-white sm:px-10">
            <a href="{{ route('home') }}" class="text-xs font-medium tracking-[0.25em] uppercase">Embodied</a>
            <nav class="absolute left-1/2 hidden -translate-x-1/2 gap-10 text-[11px] tracking-[0.2em] uppercase md:flex">
                <a href="{{ route('collection') }}" class="hover:text-white/70">Collection</a>
                <a href="#studio" class="hover:text-white/70">Studio</a>
                <a href="#lookbook" class="hover:text-white/70">Lookbook</a>
                <a href="#about" class="hover:text-white/70">About</a>
            </nav>
            <div class="flex items-center gap-4 sm:gap-6">
                <x-cart-trigger class="relative text-white" />
                <x-auth-nav-link />
                <button type="button" data-mobile-nav-toggle aria-label="Open menu" aria-expanded="false" class="md:hidden">
                    <flux:icon.bars-2 class="h-5 w-5" />
                </button>
            </div>
        </header>

        <div data-mobile-nav class="fixed inset-0 z-30 hidden bg-neutral-900/98 md:hidden">
            <button type="button" data-mobile-nav-close aria-label="Close menu" class="absolute top-6 right-6 text-white sm:right-10">
                <flux:icon.x-mark class="h-6 w-6" />
            </button>
            <nav class="flex h-full flex-col items-center justify-center gap-8 px-6 text-center">
                <a href="{{ route('collection') }}" data-mobile-nav-link class="text-lg tracking-[0.2em] text-white uppercase hover:text-white/70">Collection</a>
                <a href="#studio" data-mobile-nav-link class="text-lg tracking-[0.2em] text-white uppercase hover:text-white/70">Studio</a>
                <a href="#lookbook" data-mobile-nav-link class="text-lg tracking-[0.2em] text-white uppercase hover:text-white/70">Lookbook</a>
                <a href="#about" data-mobile-nav-link class="text-lg tracking-[0.2em] text-white uppercase hover:text-white/70">About</a>
            </nav>
        </div>

        <div class="relative z-10 flex h-full flex-col items-center justify-center px-6 text-center text-white">
            <p class="mb-4 text-xs tracking-[0.35em] text-white/60 uppercase">Clothing &middot; Indonesia &middot; Est. 2024</p>
            <h1 class="font-serif text-6xl tracking-wide sm:text-7xl md:text-8xl">EMBODIED</h1>
            <p class="mt-6 text-lg tracking-[0.2em] text-white/70 italic font-marquee">Wear What You Are</p>

            <div class="mt-10 flex flex-col gap-3 sm:flex-row">
                <a href="#collection" class="border border-white bg-cream px-8 py-3 text-[11px] tracking-[0.2em] text-neutral-900 uppercase hover:bg-cream/90">Shop Collection</a>
                <a href="#studio" class="border border-white/70 px-8 py-3 text-[11px] tracking-[0.2em] text-white uppercase hover:bg-white/10">Our Story</a>
            </div>
        </div>

        <div class="absolute bottom-8 left-1/2 z-10 flex -translate-x-1/2 flex-col items-center gap-2 text-white/60">
            <span class="text-[10px] tracking-[0.3em] uppercase">Scroll</span>
            <span class="h-8 w-px bg-white/40"></span>
        </div>
    </section>

    {{-- Ticker --}}
    <x-marquee :items="['wear what you are', 'dressed in intention', 'the body, the cloth, the self', 'quietly worn, deeply felt', 'new arrival']" />

    {{-- Collection --}}
    <section id="collection" class="mx-auto max-w-7xl px-6 py-24 sm:px-10">
        <div class="mb-10 flex items-end justify-between">
            <div>
                <p class="mb-2 text-xs tracking-[0.3em] text-neutral-500 uppercase">New Arrival</p>
                <h2 class="font-serif text-4xl">Collection</h2>
            </div>
            <a href="{{ route('collection') }}" class="text-xs tracking-[0.2em] text-neutral-600 uppercase underline underline-offset-4 hover:text-neutral-900">View All</a>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <x-product-teaser
                :name="$newArrivals->first()->name"
                :price="$newArrivals->first()->formatted_price"
                :badge="$newArrivals->first()->badge"
                :gradient="$newArrivals->first()->gradient"
                :image="$newArrivals->first()->image_url"
                :href="route('product.show', $newArrivals->first())"
                image-class="aspect-4/5 lg:aspect-auto lg:h-full"
                class="bg-neutral-200 lg:row-span-2"
            />

            <div class="grid grid-cols-2 gap-4 lg:col-span-2">
                @foreach ($newArrivals->slice(1) as $product)
                    <x-product-teaser
                        :name="$product->name"
                        :price="$product->formatted_price"
                        :gradient="$product->gradient"
                        :image="$product->image_url"
                        :href="route('product.show', $product)"
                        class="bg-neutral-100"
                    />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Studio notes --}}
    <section id="studio" class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-12 px-6 pb-24 sm:px-10 lg:grid-cols-2">
        <div class="aspect-4/5 w-full max-w-md overflow-hidden bg-neutral-200">
            <img src="{{ $aboutImage }}" alt="Embodied studio founder wearing a black polo and tailored trousers" class="h-full w-full object-cover">
        </div>

        <div>
            <p class="mb-3 text-xs tracking-[0.3em] text-neutral-500 uppercase">Studio Notes</p>
            <h3 class="mb-6 font-serif text-3xl leading-snug sm:text-4xl">&ldquo;Clothing that becomes a second skin.&rdquo;</h3>
            <p class="mb-4 text-sm leading-relaxed text-neutral-600">We work with natural fibres — linen, cotton, tencel — that breathe with the body and soften with wear. Every piece is cut to move, not to restrict.</p>
            <p class="mb-6 text-sm leading-relaxed text-neutral-600">Embodied was born from a desire to dress with intention. Slow production, limited quantities, thoughtful design. Nothing more.</p>
            <p class="mb-4 text-xs text-neutral-500 italic">— Embodied Studio, Jakarta</p>
            <a href="#about" class="text-xs tracking-[0.2em] text-neutral-900 uppercase underline underline-offset-4">Read Our Story</a>
        </div>
    </section>

    {{-- Ticker --}}
    <x-marquee :items="['embodied', 'clothing · indonesia', 'wear what you are', 'natural fibres', 'slow fashion']" />

    {{-- Lookbook --}}
    <section id="lookbook" class="px-6 py-24 sm:px-10">
        <div class="mx-auto max-w-7xl text-center">
            <p class="mb-2 text-xs tracking-[0.3em] text-neutral-500 uppercase">Collection</p>
            <h2 class="font-serif text-4xl">The Lookbook</h2>

            <div class="mt-12 grid grid-cols-1 gap-6 text-left md:grid-cols-3">
                @forelse ($lookbook as $product)
                    <x-lookbook-card
                        :label="$product->name"
                        :price="$product->formatted_price"
                        :gradient="$product->gradient"
                        :image="$product->image_url"
                        :href="route('product.show', $product)"
                    />
                @empty
                    <p class="text-sm text-neutral-500 md:col-span-3">New looks are on their way.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer id="about" class="bg-black px-6 py-16 text-white sm:px-10">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-10 lg:grid-cols-4">
            <div class="col-span-2 lg:col-span-1">
                <p class="mb-3 text-xs font-medium tracking-[0.25em] uppercase">Embodied</p>
                <p class="mb-6 text-sm text-white/70">Wear what you are.</p>
                <div class="flex gap-4 text-[11px] tracking-[0.15em] text-white/50 uppercase" aria-label="Social links coming soon">
                    <span>Instagram</span>
                    <span>TikTok</span>
                    <span>Pinterest</span>
                </div>
            </div>

            <x-footer-links title="Shop" :links="[
                'Collection' => route('collection'),
                'New Arrivals' => route('collection', ['category' => 'new']),
                'Bestsellers' => route('collection', ['category' => 'bestseller']),
            ]" />

            <x-footer-links title="Studio" :links="[
                'Our Story' => '#studio',
                'Process' => '#studio',
                'Materials' => '#studio',
            ]" />

            <x-footer-links title="Help" :links="[
                'Sizing Guide' => route('help') . '#sizing',
                'Shipping' => route('help') . '#shipping',
                'Returns' => route('help') . '#returns',
                'Contact' => route('help') . '#contact',
            ]" />
        </div>

        <div class="mx-auto mt-12 flex max-w-7xl flex-col gap-2 border-t border-white/10 pt-6 text-xs text-white/60 sm:flex-row sm:justify-between">
            <span>&copy; {{ date('Y') }} Embodied Studio, Jakarta, Indonesia.</span>
            <span>Handcrafted with intention.</span>
        </div>
    </footer>
</x-layout>
