@php
    $dashboardRoute = match (auth()->user()->role->name) {
        'admin' => 'admin.dashboard',
        'guru' => 'guru.dashboard',
        default => 'siswa.dashboard',
    };
@endphp

<header class="dashboard-navbar">
    <a class="dashboard-navbar-brand" href="{{ route($dashboardRoute) }}" aria-label="Nextask, beranda">
        <img src="{{ asset('images/brand/logo.svg') }}" alt="">
        <span>Nextask</span>
    </a>

    <nav class="dashboard-navbar-links" aria-label="Navigasi utama">
        <a
            class="dashboard-navbar-link {{ $active === 'home' ? 'is-active' : '' }}"
            href="{{ route($dashboardRoute) }}"
            @if ($active === 'home') aria-current="page" @endif
        >Beranda</a>
        <span class="dashboard-navbar-link is-unavailable" aria-disabled="true">Tugas</span>
        <span class="dashboard-navbar-link is-unavailable" aria-disabled="true">Team</span>
        <span class="dashboard-navbar-link is-unavailable" aria-disabled="true">Profil</span>
    </nav>

    <div class="dashboard-navbar-account">
        <span class="dashboard-navbar-name">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="dashboard-navbar-logout" type="submit">Keluar</button>
        </form>
    </div>
</header>
