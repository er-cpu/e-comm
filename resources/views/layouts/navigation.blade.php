@php
    $isHome = request()->routeIs('home');
    $isProducts = request()->routeIs('products.*');
    $isTrust = request()->routeIs('trust');
    $isPrivacy = request()->routeIs('privacy');
    $isCart = request()->routeIs('cart.*');
@endphp

<nav id="mainNavbar" class="navbar navbar-expand-lg fixed-top shadow-sm" style="background-color: #1a4d35; min-height: 60px;">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-1 fw-bold text-decoration-none" href="{{ route('home') }}" style="line-height: 1;">
            <span style="color: #ffffff; font-size: 1.65rem; letter-spacing: -0.03em;">SmartTrade</span>
            <span style="color: #f6ad3c; font-size: 1.65rem; letter-spacing: -0.03em;">Africa</span>
        </a>

        <button class="navbar-toggler d-lg-none border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNav" aria-controls="offcanvasNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="d-none d-lg-flex align-items-center justify-content-center flex-grow-1">
            <ul class="navbar-nav desktop-top-nav gap-2 gap-xl-4 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link {{ $isHome ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $isProducts ? 'active' : '' }}" href="{{ route('products.index') }}">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $isTrust ? 'active' : '' }}" href="{{ route('trust') }}">Trust &amp; Security</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $isPrivacy ? 'active' : '' }}" href="{{ route('privacy') }}">Privacy</a>
                </li>
            </ul>
        </div>

        <div class="d-none d-lg-flex align-items-center gap-3">
            <a class="cart-pill d-inline-flex align-items-center justify-content-center position-relative text-decoration-none" href="{{ route('cart.index') }}" aria-label="Cart">
                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5m3.102 3 1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
                </svg>
                @if($isCart)
                    <span class="cart-badge position-absolute top-0 start-100 translate-middle badge rounded-pill">1</span>
                @endif
            </a>

            @guest
                <a class="btn btn-outline-light btn-sm px-4 py-2 rounded-3" href="{{ route('login') }}">Log In</a>
                <a class="btn btn-warning btn-sm px-4 py-2 rounded-3 text-dark fw-semibold" href="{{ route('register') }}">Sign Up</a>
            @else
                <div class="dropdown">
                    <button class="btn btn-outline-light btn-sm rounded-3 px-3 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->first_name ?? 'Account' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{ route('history.index') }}">History</a></li>
                        <li><a class="dropdown-item" href="{{ route('notifications.index') }}">Notifications</a></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                        @if(Auth::user()->isAdmin())
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Admin Panel</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger" type="submit">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endguest
        </div>
    </div>
</nav>

<div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="offcanvasNav" aria-labelledby="offcanvasNavLabel">
    <div class="offcanvas-header border-bottom border-secondary border-opacity-10">
        <a class="navbar-brand d-flex align-items-center gap-1 fw-bold text-decoration-none" href="{{ route('home') }}">
            <span style="color: #ffffff; font-size: 1.4rem; letter-spacing: -0.03em;">SmartTrade</span>
            <span style="color: #f6ad3c; font-size: 1.4rem; letter-spacing: -0.03em;">Africa</span>
        </a>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body px-0">
        @guest
            <div class="px-3 pb-3 d-flex gap-2">
                <a class="btn btn-outline-light btn-sm px-4 py-2 rounded-3 flex-fill" href="{{ route('login') }}">Log In</a>
                <a class="btn btn-warning btn-sm px-4 py-2 rounded-3 text-dark fw-semibold flex-fill" href="{{ route('register') }}">Sign Up</a>
            </div>
        @endguest
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link px-3 py-2 {{ $isHome ? 'active fw-bold' : '' }}" href="{{ route('home') }}">Home</a></li>
            <li class="nav-item"><a class="nav-link px-3 py-2 {{ $isProducts ? 'active fw-bold' : '' }}" href="{{ route('products.index') }}">Products</a></li>
            <li class="nav-item"><a class="nav-link px-3 py-2 {{ $isTrust ? 'active fw-bold' : '' }}" href="{{ route('trust') }}">Trust &amp; Security</a></li>
            <li class="nav-item"><a class="nav-link px-3 py-2 {{ $isPrivacy ? 'active fw-bold' : '' }}" href="{{ route('privacy') }}">Privacy</a></li>
            <li class="nav-item"><a class="nav-link px-3 py-2 {{ $isCart ? 'active fw-bold' : '' }}" href="{{ route('cart.index') }}">Cart</a></li>
            @guest
                <li class="nav-item"><a class="nav-link px-3 py-2" href="{{ route('login') }}">Log In</a></li>
                <li class="nav-item"><a class="nav-link px-3 py-2" href="{{ route('register') }}">Sign Up</a></li>
            @else
                @if(Auth::user()->isAdmin())
                    <li class="nav-item"><a class="nav-link px-3 py-2" href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                @endif
                <li class="nav-item"><a class="nav-link px-3 py-2" href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link px-3 py-2" href="{{ route('history.index') }}">History</a></li>
                <li class="nav-item"><a class="nav-link px-3 py-2" href="{{ route('notifications.index') }}">Notifications</a></li>
                <li class="nav-item"><a class="nav-link px-3 py-2" href="{{ route('profile.edit') }}">Profile</a></li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="nav-link px-3 py-2 text-danger border-0 bg-transparent w-100 text-start" type="submit">Logout</button>
                    </form>
                </li>
            @endguest
        </ul>
    </div>
</div>

<style>
    #mainNavbar {
        z-index: 1045;
    }

    #mainNavbar .dropdown-menu {
        z-index: 1055;
    }

    .desktop-top-nav .nav-link {
        color: rgba(255,255,255,0.88);
        font-weight: 500;
        font-size: 0.95rem;
        position: relative;
        padding: 0.35rem 0.15rem;
    }

    .desktop-top-nav .nav-link:hover,
    .desktop-top-nav .nav-link.active {
        color: #ffffff;
    }

    .desktop-top-nav .nav-link::after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: -10px;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: #ffffff;
        transition: width 0.25s ease;
    }

    .desktop-top-nav .nav-link:hover::after,
    .desktop-top-nav .nav-link.active::after {
        width: 100%;
    }

    .cart-pill {
        width: 42px;
        height: 42px;
        border-radius: 999px;
        color: #ffffff;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .cart-pill:hover {
        background: rgba(255,255,255,0.12);
        color: #ffffff;
        transform: translateY(-1px);
    }

    .cart-badge {
        background: #f6ad3c;
        color: #1a1a1a;
        font-size: 0.6rem;
        min-width: 1rem;
        min-height: 1rem;
        padding: 0.15rem 0.25rem;
        line-height: 1;
        border: 2px solid #1a4d35;
    }

</style>
