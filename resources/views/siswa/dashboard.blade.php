@extends('layouts.dashboard')

@section('title', 'Dashboard Siswa - Nextask')

@section('content')
            <div class="student-dashboard-content">
                <header class="student-dashboard-heading">
                    <p class="student-dashboard-eyebrow">Ruang belajar Anda</p>
                    <h1>Selamat datang, {{ auth()->user()->name }}</h1>
                    <p>Semua informasi belajar Anda akan tersedia di sini.</p>
                </header>

                <section class="student-task-panel" id="student-tasks" aria-labelledby="student-task-title">
                    <div class="student-task-content">
                        <span class="student-section-label">Tugas untuk dilanjutkan</span>
                        <h2 id="student-task-title">Belum ada tugas</h2>
                        <p>Tugas yang diberikan guru akan muncul di sini.</p>
                    </div>

                    <div class="student-task-illustration" aria-hidden="true">
                        <div class="student-task-orbit student-task-orbit-one"></div>
                        <div class="student-task-orbit student-task-orbit-two"></div>
                        <svg viewBox="0 0 220 180" fill="none">
                            <path d="M37 129.5 77.5 82l35.8 22.5L157 56" stroke="#8DB6EA" stroke-width="2" stroke-linecap="round" stroke-dasharray="5 7"/>
                            <circle cx="158" cy="56" r="20" fill="#E4F0FF"/>
                            <path d="m150 56 5.2 5.2L166 49" stroke="#2668B5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            <rect x="46" y="65" width="91" height="89" rx="14" fill="white" stroke="#D8E5F4" stroke-width="2"/>
                            <rect x="60" y="82" width="63" height="9" rx="4.5" fill="#DCE8F5"/>
                            <rect x="60" y="101" width="50" height="7" rx="3.5" fill="#E8EFF7"/>
                            <rect x="60" y="117" width="56" height="7" rx="3.5" fill="#E8EFF7"/>
                            <circle cx="63.5" cy="138.5" r="4.5" fill="#BBD5F3"/>
                            <rect x="73" y="135" width="41" height="7" rx="3.5" fill="#E8EFF7"/>
                            <path d="m34 130 21-4 4 21-21 4-4-21Z" fill="#D9EAFE"/>
                            <path d="m39 137 5 5 9-11" stroke="#2668B5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </section>

                <section class="student-statistics" aria-labelledby="student-statistics-title">
                    <div class="student-section-heading">
                        <div>
                            <span class="student-section-label">Ringkasan</span>
                            <h2 id="student-statistics-title">Statistik belajar</h2>
                        </div>
                    </div>

                    <div class="student-statistics-grid">
                        <article class="student-stat-card">
                            <span class="student-stat-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7 3.75h7l4 4v12.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z"/>
                                    <path d="M14 4v4h4M9 12h6M9 16h6"/>
                                </svg>
                            </span>
                            <div>
                                <p class="student-stat-label">Tugas saat ini</p>
                                <p class="student-stat-value">0</p>
                                <p class="student-stat-note">Belum ada tugas yang diberikan.</p>
                            </div>
                        </article>
                        <p class="student-statistics-note">Statistik lainnya akan tersedia setelah ada data tugas.</p>
                    </div>
                </section>

                <p class="student-dashboard-quote">“Belajar lebih teratur, satu langkah setiap hari.”</p>
            </div>
@endsection
