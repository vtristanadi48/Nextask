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
            ['label' => 'Administrasi', 'icon' => 'administration', 'route' => null],
            ['label' => 'Akademik', 'icon' => 'calendar', 'route' => null],
            ['label' => 'Kelas', 'icon' => 'classes', 'route' => 'admin.kelas'],
            ['label' => 'Chat', 'icon' => 'chat', 'route' => null],
            ['label' => 'Pusat Bantuan', 'icon' => 'help', 'route' => null],
        ],
        'guru' => [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => $dashboardRoute],
            ['label' => 'Tahun Ajaran', 'icon' => 'calendar', 'route' => null],
            ['label' => 'Konten Mengajar', 'icon' => 'content', 'route' => null],
            ['label' => 'Chat', 'icon' => 'chat', 'route' => null],
            ['label' => 'Pusat Bantuan', 'icon' => 'help', 'route' => null],
        ],
        'siswa' => [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => $dashboardRoute],
            ['label' => 'Tahun Ajaran', 'icon' => 'calendar', 'route' => null],
            ['label' => 'Tugas', 'icon' => 'tasks', 'route' => null],
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
            @if ($item['route'] !== null)
                <a
                    class="dashboard-sidebar-link {{ request()->routeIs($item['route']) ? 'is-active' : '' }}"
                    href="{{ route($item['route']) }}"
                    @if (request()->routeIs($item['route'])) aria-current="page" @endif
                >
            @else
                <a
                    class="dashboard-sidebar-link is-unavailable"
                    href="#"
                    data-feature-notice="Fitur ini sedang dalam tahap pengembangan"
                    aria-label="{{ $item['label'] }}, fitur sedang dalam tahap pengembangan"
                >
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
                        @case('administration')
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z"/>
                                <path d="M14 4v4h4M9 12h6M9 16h6M3 7v12a2 2 0 0 0 2 2"/>
                            </svg>
                            @break
                        @case('calendar')
                            <svg viewBox="0 0 24 24" fill="none">
                                <rect x="4" y="5.5" width="16" height="15" rx="2"/>
                                <path d="M8 3.5v4M16 3.5v4M4 10h16M8 14h3M8 17h3"/>
                            </svg>
                            @break
                        @case('classes')
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M3.5 20V8l8.5-4 8.5 4v12M2.5 20h19"/>
                                <path d="M8 20v-5h8v5M7.5 9.5h.01M12 9.5h.01M16.5 9.5h.01" stroke-linecap="round"/>
                            </svg>
                            @break
                        @case('content')
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21V5.5Z"/>
                                <path d="M4 18.5A2.5 2.5 0 0 1 6.5 16H20M8 7h8M8 10h8"/>
                            </svg>
                            @break
                        @case('tasks')
                            <svg viewBox="0 0 24 24" fill="none">
                                <rect x="5" y="4.5" width="14" height="17" rx="2"/>
                                <path d="M9 4.5v-1h6v1M8.5 10h7M8.5 14h7M8.5 18h4"/>
                                <path d="m8 7 1 1 2-2"/>
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
                @if ($item['route'] === null)
                    <span class="ml-auto rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500">Segera</span>
                @endif
            @if ($item['route'] !== null)
                </a>
            @else
                </a>
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
