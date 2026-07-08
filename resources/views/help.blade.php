<x-layout :title="'Help — ' . config('app.name', 'Embodied')">

    <header class="flex items-center justify-between border-b border-neutral-200 px-6 py-5 sm:px-10">
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-xs font-medium tracking-[0.25em] uppercase">
            <flux:icon.arrow-left class="h-4 w-4" /> Embodied
        </a>

        <h1 class="font-serif text-xl">Help</h1>

        <x-cart-trigger class="relative text-neutral-900" />
    </header>

    <div class="mx-auto max-w-3xl px-6 py-12 sm:px-10">
        <nav class="mb-16 flex flex-wrap gap-x-6 gap-y-2 border-b border-neutral-200 pb-8 text-xs tracking-[0.15em] uppercase">
            <a href="#sizing" class="underline underline-offset-4 hover:text-neutral-600">Sizing Guide</a>
            <a href="#shipping" class="underline underline-offset-4 hover:text-neutral-600">Shipping</a>
            <a href="#returns" class="underline underline-offset-4 hover:text-neutral-600">Returns</a>
            <a href="#contact" class="underline underline-offset-4 hover:text-neutral-600">Contact</a>
        </nav>

        <section id="sizing" class="mb-16 scroll-mt-8">
            <p class="mb-2 text-xs tracking-[0.3em] text-neutral-500 uppercase">Help</p>
            <h2 class="mb-6 font-serif text-3xl">Sizing Guide</h2>
            <p class="mb-6 text-sm leading-relaxed text-neutral-600">
                Our pieces are cut with a relaxed, oversized silhouette. If you're between sizes, we recommend sizing up for a fuller drape.
            </p>

            <div class="overflow-x-auto border border-neutral-200">
                <table class="w-full min-w-[420px] text-left text-sm">
                    <thead class="border-b border-neutral-200 text-xs tracking-[0.1em] text-neutral-500 uppercase">
                        <tr>
                            <th scope="col" class="px-4 py-3">Size</th>
                            <th scope="col" class="px-4 py-3">Chest (cm)</th>
                            <th scope="col" class="px-4 py-3">Waist (cm)</th>
                            <th scope="col" class="px-4 py-3">Length (cm)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        <tr><td class="px-4 py-3">XS</td><td class="px-4 py-3">84–88</td><td class="px-4 py-3">64–68</td><td class="px-4 py-3">62</td></tr>
                        <tr><td class="px-4 py-3">S</td><td class="px-4 py-3">89–93</td><td class="px-4 py-3">69–73</td><td class="px-4 py-3">64</td></tr>
                        <tr><td class="px-4 py-3">M</td><td class="px-4 py-3">94–98</td><td class="px-4 py-3">74–78</td><td class="px-4 py-3">66</td></tr>
                        <tr><td class="px-4 py-3">L</td><td class="px-4 py-3">99–104</td><td class="px-4 py-3">79–84</td><td class="px-4 py-3">68</td></tr>
                        <tr><td class="px-4 py-3">XL</td><td class="px-4 py-3">105–110</td><td class="px-4 py-3">85–90</td><td class="px-4 py-3">70</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="shipping" class="mb-16 scroll-mt-8">
            <p class="mb-2 text-xs tracking-[0.3em] text-neutral-500 uppercase">Help</p>
            <h2 class="mb-6 font-serif text-3xl">Shipping</h2>
            <div class="space-y-4 text-sm leading-relaxed text-neutral-600">
                <p>Every piece is made in small batches — please allow 3–5 business days for your order to be prepared before it ships.</p>
                <p>We ship nationwide across Indonesia via JNE and J&amp;T. Estimated delivery is 2–5 business days for Java and 4–8 business days for other islands, depending on your location.</p>
                <p>You'll receive your order tracking details once your payment has been confirmed and your package has shipped.</p>
            </div>
        </section>

        <section id="returns" class="mb-16 scroll-mt-8">
            <p class="mb-2 text-xs tracking-[0.3em] text-neutral-500 uppercase">Help</p>
            <h2 class="mb-6 font-serif text-3xl">Returns &amp; Exchanges</h2>
            <div class="space-y-4 text-sm leading-relaxed text-neutral-600">
                <p>We accept returns and exchanges within 7 days of delivery, provided the item is unworn, unwashed, and still has its original tags attached.</p>
                <p>To start a return, reach out to us via the contact details below with your order number and the reason for return.</p>
                <p>Sale items and accessories are final sale and cannot be returned or exchanged.</p>
            </div>
        </section>

        <section id="contact" class="scroll-mt-8">
            <p class="mb-2 text-xs tracking-[0.3em] text-neutral-500 uppercase">Help</p>
            <h2 class="mb-6 font-serif text-3xl">Contact</h2>
            <div class="space-y-2 text-sm leading-relaxed text-neutral-600">
                <p>Email: <span class="text-neutral-900">hello@embodied.studio</span></p>
                <p>Studio hours: Monday–Friday, 09.00–17.00 WIB</p>
                <p>Embodied Studio, Jakarta, Indonesia</p>
            </div>
        </section>
    </div>
</x-layout>
