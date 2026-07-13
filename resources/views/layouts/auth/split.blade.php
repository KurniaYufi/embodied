<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="h-dvh overflow-hidden bg-cream antialiased dark:bg-neutral-900">
        <div class="grid h-dvh grid-cols-1 lg:grid-cols-2">
            {{-- Form side --}}
            <div class="flex flex-col px-10 py-8 sm:px-6">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-xs font-medium tracking-[0.25em] text-neutral-900 uppercase dark:text-neutral-100" wire:navigate>
                    Embodied
                </a>

                <div class="flex flex-1 items-center justify-center overflow-y-auto py-8">
                    <div class="mx-auto w-full max-w-sm">
                        {{ $slot }}
                    </div>
                </div>
            </div>

            {{-- Image side --}}
            <div class="relative hidden overflow-hidden bg-neutral-900 lg:block">
                <img
                    src="{{ \Illuminate\Support\Facades\Storage::disk('supabase')->url('site/about.jpg') }}"
                    alt=""
                    class="h-full w-full object-cover object-top"
                >
                <div class="absolute inset-0 bg-linear-to-t from-black/70 via-black/10 to-black/40"></div>

                <div class="absolute inset-x-0 bottom-0 p-10 text-white">
                    <p class="mb-2 text-xs tracking-[0.3em] text-white/60 uppercase">Clothing &middot; Indonesia</p>
                    <p class="font-serif text-3xl">Wear what you are.</p>
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
