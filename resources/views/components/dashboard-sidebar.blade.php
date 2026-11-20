@php
    $userRole = auth()->user()->role?->name;
    $dashboardRoute = match ($userRole) {
        'admin' => 'admin.dashboard',
        'guru' => 'guru.dashboard',
        'siswa' => 'siswa.dashboard',
        default => null,
    };
    $menuItemsByRole = [
        'admin' => [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => $dashboardRoute],
            ['label' => 'Administrasi Akademik', 'icon' => 'academic', 'route' => null],
            ['label' => 'Kelas', 'icon' => 'classes', 'route' => null],
            ['label' => 'Chat', 'icon' => 'chat', 'route' => null],
            ['label' => 'Pusat Bantuan', 'icon' => 'help', 'route' => null],
        ],
        'guru' => [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => $dashboardRoute],
            ['label' => 'Kelas', 'icon' => 'classes', 'route' => null],
            ['label' => 'Chat', 'icon' => 'chat', 'route' => null],
            ['label' => 'Pusat Bantuan', 'icon' => 'help', 'route' => null],
        ],
        'siswa' => [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => $dashboardRoute],
            ['label' => 'Kelas', 'icon' => 'classes', 'route' => null],
            ['label' => 'Chat', 'icon' => 'chat', 'route' => null],
            ['label' => 'Pusat Bantuan', 'icon' => 'help', 'route' => null],
        ],
    ];
    $menuItems = $menuItemsByRole[$userRole] ?? [];
@endphp

<aside class="dashboard-sidebar" id="dashboard-sidebar" aria-label="Navigasi dashboard">
    <p class="dashboard-sidebar-label">Menu utama</p>

    <nav class="dashboard-sidebar-nav">
        @foreach ($menuItems as $item)
            @if ($item['route'] !== null && Route::has($item['route']))
                <a
                    class="dashboard-sidebar-link {{ request()->routeIs($item['route']) ? 'is-active' : '' }}"
                    href="{{ route($item['route']) }}"
                    @if (request()->routeIs($item['route'])) aria-current="page" @endif
                >
            @else
                <span class="dashboard-sidebar-link is-unavailable" aria-disabled="true" title="Fitur belum tersedia">
            @endif
                <span class="dashboard-sidebar-icon" aria-hidden="true">
                    @switch($item['icon'])
                        @case('dashboard')
                            <svg viewBox="0 0 24 24" fill="none">
                                <rect x="3.75" y="3.75" width="6.5" height="6.5" rx="1.5"/>
                                <rect x="13.75" y="3.75" width="6.5" height="6.5" rx="1.5"/>
                                <rect x="3.75" y="13.75" width="6.5" height="6.5" rx="1.5"/>
                                <rect x="13.75" y="13.75" width="6.5" height="6.5" rx="1.5"/>
                            </svg>
                            @break
                        @case('academic')
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z"/>
                                <path d="M14 4v4h4M9 12h6M9 16h6"/>
                            </svg>
                            @break
                        @case('classes')
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20m6-9a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm6-6.75a3.5 3.5 0 0 1 0 6.75m1 4h1.5a3.5 3.5 0 0 1 3.5 3.5V20"/>
                            </svg>
                            @break
                        @case('chat')
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M20 11.5a7.5 7.5 0 0 1-7.5 7.5H6l-3 2v-9.5A7.5 7.5 0 0 1 10.5 4H12a8 8 0 0 1 8 7.5Z"/>
                                <path d="M8 11h.01M12 11h.01M16 11h.01" stroke-width="2.5" stroke-linecap="round"/>
                            </svg>
                            @break
                        @case('help')
                            <svg viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="8.25"/>
                                <path d="M9.75 9.5a2.3 2.3 0 1 1 3.9 1.65c-.9.85-1.65 1.25-1.65 2.6m0 3h.01" stroke-linecap="round"/>
                            </svg>
                            @break
                    @endswitch
                </span>
                <span>{{ $item['label'] }}</span>
            @if ($item['route'] !== null && Route::has($item['route']))
                </a>
            @else
                </span>
            @endif
        @endforeach
    </nav>

    <div class="dashboard-sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="dashboard-navbar-logout dashboard-sidebar-logout" type="submit">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M10 5H6.5A1.5 1.5 0 0 0 5 6.5v11A1.5 1.5 0 0 0 6.5 19H10m4-3 4-4-4-4m4 4H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
