@if (Route::has('login'))
    @auth
        <a href="{{ route('dashboard') }}" {{ $attributes->merge(['class' => 'border border-white/60 px-4 py-1.5 text-[11px] tracking-[0.2em] text-white uppercase hover:bg-white/10']) }}>Dashboard</a>
    @else
        <a href="{{ route('login') }}" {{ $attributes->merge(['class' => 'border border-white/60 px-4 py-1.5 text-[11px] tracking-[0.2em] text-white uppercase hover:bg-white/10']) }}>Login</a>
    @endauth
@endif
