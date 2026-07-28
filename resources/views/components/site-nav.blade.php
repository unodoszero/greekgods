@props(['showLogout' => false])

<nav aria-label="Primary navigation">
    <button class="nav-menu-button" id="nav-menu-button" type="button" aria-label="Toggle navigation" aria-controls="nav-links" aria-expanded="false">
        <img src="/graphics/svg/menu-black.svg" alt="">
    </button>

    <a class="nav-logo" href="/" aria-label="GreekGods home">
        <img src="/graphics/logo/greekgodslogo.png" alt="GreekGods">
    </a>

    <a class="nav-menu-profile" id="nav-menu-profile" href="{{ Auth::check() ? '/profile' : '/register' }}" aria-label="{{ Auth::check() ? 'Open profile' : 'Create account' }}">
        <img src="/graphics/svg/profile.svg" alt="">
    </a>

    <ul class="nav-links" id="nav-links">
        @foreach ([
            '/' => 'HOME',
            '/program' => 'PROGRAM',
            '/blog' => 'BLOG',
            '/calculator' => 'CALCULATOR',
            '/about' => 'ABOUT',
        ] as $path => $label)
            <li><a href="{{ $path }}" @if(request()->is(ltrim($path, '/')) || ($path === '/' && request()->is('/'))) aria-current="page" @endif>{{ $label }}</a></li>
        @endforeach
    </ul>

    <div class="nav-button">
        @auth
            @if ($showLogout)
                <button id="logout" type="button">LOGOUT</button>
            @else
                <a id="profile-button" href="/profile" aria-label="Open profile">
                    <img src="/graphics/svg/profile.svg" alt="">
                </a>
                <a id="profile-name" href="/profile">{{ trim(Auth::user()->first_name.' '.Auth::user()->last_name) }}</a>
            @endif
        @else
            <a id="register-button" href="/register">GET STARTED</a>
        @endauth
    </div>
</nav>
