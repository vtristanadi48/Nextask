@php
    $user = auth()->user();
    $dashboardRoute = match ($user->role?->name) {
        'admin' => 'admin.dashboard',
        'guru' => 'guru.dashboard',
        default => 'siswa.dashboard',
    };
    $avatarUrl = data_get($user, 'avatar_url')
        ?? data_get($user, 'profile_photo_url')
        ?? data_get($user, 'photo_url');
    $roleLabel = match ($user->role?->name) {
        'admin' => 'Administrator',
        'guru' => 'Guru',
        'siswa' => 'Siswa',
        default => 'Pengguna',
    };
    $userInitials = collect(explode(' ', trim($user->name)))
        ->filter()
        ->take(2)
        ->map(fn (string $part): string => mb_substr($part, 0, 1))
        ->implode('');
@endphp

<header class="dashboard-navbar">
    <div class="dashboard-navbar-start">
        <button
            class="dashboard-navbar-toggle"
            type="button"
            data-sidebar-toggle
            aria-controls="dashboard-sidebar"
            aria-expanded="false"
            aria-label="Buka navigasi"
        >
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>

        <a class="dashboard-navbar-brand" href="{{ route($dashboardRoute) }}" aria-label="Nextask, beranda">
            <img src="{{ asset('images/brand/logo.svg') }}" alt="">
            <span>Nextask</span>
        </a>
    </div>

    <div class="dashboard-navbar-end">
        <div class="dashboard-navbar-actions" role="group" aria-label="Pesan, notifikasi, dan pengaturan">
            @include('components.dashboard-message-dropdown')
            @include('components.dashboard-notification-dropdown')
            @include('components.dashboard-settings-dropdown')
        </div>

        <div class="dashboard-navbar-account">
            <div class="dashboard-navbar-avatar" aria-hidden="true">
                @if (is_string($avatarUrl) && $avatarUrl !== '')
                    <img src="{{ $avatarUrl }}" alt="">
                @else
                    <span>{{ $userInitials }}</span>
                @endif
            </div>

            <div class="dashboard-navbar-user">
                <span class="dashboard-navbar-name">{{ $user->name }}</span>
                <span class="dashboard-navbar-role">{{ $roleLabel }}</span>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="dashboard-navbar-logout" type="submit">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M10 5H6.5A1.5 1.5 0 0 0 5 6.5v11A1.5 1.5 0 0 0 6.5 19H10m4-3 4-4-4-4m4 4H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</header>
