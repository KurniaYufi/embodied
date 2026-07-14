@if (Route::has('login'))
    @auth
        @if (auth()->user()->isAdmin())
            <a href="{{ route('dashboard') }}" {{ $attributes->merge(['class' => 'border border-white/60 px-4 py-1.5 text-[11px] tracking-[0.2em] text-white uppercase hover:bg-white/10']) }}>Dashboard</a>
        @else
            <a href="{{ route('orders.index') }}" {{ $attributes->merge(['class' => 'border border-white/60 px-4 py-1.5 text-[11px] tracking-[0.2em] text-white uppercase hover:bg-white/10']) }}>My Orders</a>
        @endif
    @else
        <a href="{{ route('login') }}" {{ $attributes->merge(['class' => 'border border-white/60 px-4 py-1.5 text-[11px] tracking-[0.2em] text-white uppercase hover:bg-white/10']) }}>Login</a>
    @endauth
@endif
