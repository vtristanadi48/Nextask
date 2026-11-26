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

            <div class="flex flex-col gap-6">
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
