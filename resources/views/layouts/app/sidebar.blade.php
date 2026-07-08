<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        {{-- This admin area is styled light-only to match the storefront's design system, so
             it never adopted dark: variants. @fluxAppearance below re-applies dark mode on
             every Livewire navigation when the visitor's OS prefers it, which would make our
             uncolored text invisible — pin Flux's own stored preference to "light" (the same
             mechanism the real Appearance settings page uses) so it stays light everywhere. --}}
        <script>window.localStorage.setItem('flux.appearance', 'light')</script>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-cream">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-neutral-200 bg-cream">
            <flux:sidebar.header>
                <a href="{{ route('dashboard') }}" wire:navigate class="flex h-10 min-w-0 flex-1 items-center px-2 text-xs font-medium tracking-[0.25em] text-neutral-900 uppercase in-data-flux-sidebar-collapsed-desktop:hidden">
                    Embodied
                </a>
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>

                    <flux:sidebar.group :heading="__('Master Data')" icon="archive-box" expandable :expanded="request()->routeIs('seller.products', 'seller.categories')">
                        <flux:sidebar.item :href="route('seller.products')" :current="request()->routeIs('seller.products')" wire:navigate>
                            {{ __('Products') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item :href="route('seller.categories')" :current="request()->routeIs('seller.categories')" wire:navigate>
                            {{ __('Categories') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>

                    <flux:sidebar.item icon="banknotes" :href="route('seller.transactions')" :current="request()->routeIs('seller.transactions')" wire:navigate>
                        {{ __('Transaksi') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="border-b border-neutral-200 bg-cream lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
