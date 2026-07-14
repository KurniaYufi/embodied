@auth
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" {{ $attributes }}>Log Out</button>
    </form>
@endauth
