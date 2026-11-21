@extends('layouts.dashboard')

@section('title', 'Dashboard Admin - Nextask')

@section('content')
    <section class="mx-auto flex max-w-7xl flex-col gap-7">
        <header class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-col gap-2">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Dashboard Admin</p>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Selamat datang, {{ auth()->user()->name }}
                </h1>
                <p class="text-sm text-slate-500">Ringkasan aktivitas dan data pengguna Nextask.</p>
            </div>
            <span class="inline-flex w-fit items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                <span class="size-2 rounded-full bg-emerald-500"></span>
                Sistem Aktif
            </span>
        </header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4">
            @foreach ([
                ['label' => 'Total Siswa', 'value' => $totalStudents, 'icon' => 'students', 'tone' => 'blue'],
                ['label' => 'Total Guru', 'value' => $totalTeachers, 'icon' => 'teachers', 'tone' => 'violet'],
                ['label' => 'Total Kelas', 'value' => $totalClasses, 'icon' => 'classes', 'tone' => 'amber'],
                ['label' => 'Total Pengguna', 'value' => $totalUsers, 'icon' => 'users', 'tone' => 'emerald'],
            ] as $stat)
                <article class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-2">
                        <p class="text-sm font-medium text-slate-500">{{ $stat['label'] }}</p>
                        <p class="text-3xl font-bold tracking-tight text-slate-900">{{ number_format($stat['value']) }}</p>
                        <p class="text-xs text-slate-400">Terdaftar di Nextask</p>
                    </div>
                    <span class="grid size-12 shrink-0 place-items-center rounded-xl
                        {{ match ($stat['tone']) {
                            'blue' => 'bg-blue-50 text-blue-700',
                            'violet' => 'bg-violet-50 text-violet-700',
                            'amber' => 'bg-amber-50 text-amber-700',
                            default => 'bg-emerald-50 text-emerald-700',
                        } }}"
                        aria-hidden="true"
                    >
                        @switch($stat['icon'])
                            @case('students')
                            @case('teachers')
                                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20m6-9a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm6-6.5a3.5 3.5 0 0 1 0 6.5m1 4h1.5a3.5 3.5 0 0 1 3.5 3.5V20"/>
                                </svg>
                                @break
                            @case('classes')
                                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3.5 20V8l8.5-4 8.5 4v12M2.5 20h19M8 20v-5h8v5"/>
                                </svg>
                                @break
                            @default
                                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="9" cy="8" r="3.5"/>
                                    <path d="M2.5 20v-1.5a6.5 6.5 0 0 1 13 0V20m2-12a3.5 3.5 0 0 1 0 6.8m1 3.2a5 5 0 0 1 3 4.5"/>
                                </svg>
                        @endswitch
                    </span>
                </article>
            @endforeach
        </div>

        <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-3">
            <section class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2" aria-labelledby="recent-users-title">
                <header class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-5 sm:px-6">
                    <div class="flex flex-col gap-1">
                        <h2 class="text-lg font-bold text-slate-900" id="recent-users-title">Pengguna Terbaru</h2>
                        <p class="text-sm text-slate-500">Lima akun terakhir yang bergabung.</p>
                    </div>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">5 terbaru</span>
                </header>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3 sm:px-6">Pengguna</th>
                                <th class="px-4 py-3">Role</th>
                                <th class="px-5 py-3 sm:px-6">Waktu Bergabung</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recentUsers as $user)
                                <tr class="transition-colors hover:bg-slate-50/70">
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex items-center gap-3">
                                            <span class="grid size-10 shrink-0 place-items-center rounded-full bg-blue-50 text-xs font-bold uppercase text-blue-700" aria-hidden="true">
                                                {{ str($user->name)->trim()->explode(' ')->filter()->take(2)->map(fn (string $part): string => mb_substr($part, 0, 1))->implode('') }}
                                            </span>
                                            <span class="flex min-w-0 flex-col gap-1">
                                                <span class="truncate font-semibold text-slate-800">{{ $user->name }}</span>
                                                <span class="truncate text-xs text-slate-500">{{ $user->email }}</span>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        @php
                                            $roleBadgeClass = match ($user->role->name) {
                                                'admin' => 'bg-violet-50 text-violet-700',
                                                'guru' => 'bg-blue-50 text-blue-700',
                                                default => 'bg-emerald-50 text-emerald-700',
                                            };
                                            $roleLabel = match ($user->role->name) {
                                                'admin' => 'Admin',
                                                'guru' => 'Guru',
                                                default => 'Siswa',
                                            };
                                        @endphp
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $roleBadgeClass }}">
                                            {{ $roleLabel }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-xs text-slate-500 sm:px-6">
                                        {{ $user->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-6 py-10 text-center text-sm text-slate-500" colspan="3">
                                        Belum ada pengguna terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="flex flex-col gap-6">
                <section class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="quick-actions-title">
                    <div class="flex flex-col gap-1">
                        <h2 class="text-lg font-bold text-slate-900" id="quick-actions-title">Aksi Cepat</h2>
                        <p class="text-sm text-slate-500">Pintasan pengelolaan Nextask.</p>
                    </div>

                    <div class="flex flex-col gap-2">
                        @foreach ([
                            ['title' => 'Kelola Master Data (Kelas)', 'description' => 'Tambah guru, siswa, mapel & jurusan', 'icon' => 'classes', 'route' => 'admin.kelas'],
                            ['title' => 'Pengaturan Akademik', 'description' => 'Atur tahun ajaran & semester', 'icon' => 'calendar', 'route' => null],
                            ['title' => 'Data Administrasi', 'description' => 'Hubungkan relasi data akademik', 'icon' => 'administration', 'route' => null],
                        ] as $action)
                            <a
                                class="group flex items-center gap-3 rounded-xl border border-slate-100 p-3 transition-colors hover:border-blue-100 hover:bg-blue-50/60 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                                href="{{ $action['route'] !== null ? route($action['route']) : '#' }}"
                                @if ($action['route'] === null) data-feature-notice="Segera hadir" @endif
                            >
                                <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-slate-100 text-slate-600 transition-colors group-hover:bg-white group-hover:text-blue-700" aria-hidden="true">
                                    @switch($action['icon'])
                                        @case('classes')
                                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3.5 20V8l8.5-4 8.5 4v12M2.5 20h19M8 20v-5h8v5"/>
                                            </svg>
                                            @break
                                        @case('calendar')
                                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="4" y="5.5" width="16" height="15" rx="2"/>
                                                <path d="M8 3.5v4M16 3.5v4M4 10h16"/>
                                            </svg>
                                            @break
                                        @case('administration')
                                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z"/>
                                                <path d="M14 4v4h4M9 12h6M9 16h6"/>
                                            </svg>
                                    @endswitch
                                </span>
                                <span class="flex min-w-0 flex-1 flex-col gap-1">
                                    <span class="text-sm font-semibold text-slate-800">{{ $action['title'] }}</span>
                                    <span class="text-xs leading-relaxed text-slate-500">{{ $action['description'] }}</span>
                                </span>
                                @if ($action['route'] === null)
                                    <span class="shrink-0 rounded-full bg-slate-100 px-2 py-1 text-[10px] font-semibold text-slate-500">Segera</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </section>

                <section class="flex flex-col gap-4 rounded-2xl border border-blue-100 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="academic-status-title">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex flex-col gap-1">
                            <h2 class="text-base font-bold text-slate-900" id="academic-status-title">Status Akademik Aktif</h2>
                            <p class="text-xs text-slate-500">Periode akademik saat ini</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                            <span class="size-1.5 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                    </div>
                    <div class="rounded-xl bg-blue-50/70 p-4">
                        <p class="font-semibold text-blue-900">Tahun Ajaran 2026/2027</p>
                        <p class="mt-1 text-sm text-blue-700">Semester Ganjil</p>
                    </div>
                </section>
            </div>
        </div>
    </section>
@endsection
