<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#f5f8fc">
        <title>Masuk sebagai {{ ucfirst($role) }} - Nextask</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="login-page font-sans antialiased">
        <main class="login-layout">
            <section class="login-brand-panel" aria-label="Tentang Nextask">
                <a class="login-brand" href="{{ route('onboarding') }}" aria-label="Nextask, pilih peran">
                    <span class="login-brand-mark" aria-hidden="true">N</span>
                    <span>Nextask</span>
                </a>

                <div class="login-brand-message">
                    <p class="login-eyebrow">Sistem Manajemen Tugas Terpadu</p>
                    <h1>Belajar dan mengajar, lebih teratur.</h1>
                    <p>Masuk untuk melanjutkan aktivitas dan mengelola tugas Anda di Nextask.</p>
                </div>

                <div class="login-decoration" aria-hidden="true">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <p class="login-brand-footer">Satu ruang untuk kegiatan belajar yang lebih baik.</p>
            </section>

            <section class="login-form-panel" aria-labelledby="login-title">
                <div class="login-form-content">
                    <a class="login-mobile-brand" href="{{ route('onboarding') }}" aria-label="Nextask, pilih peran">
                        <span class="login-brand-mark" aria-hidden="true">N</span>
                        <span>Nextask</span>
                    </a>

                    <span class="login-role-badge">{{ $role === 'siswa' ? 'Akun Siswa' : 'Akun Guru' }}</span>
                    <h2 id="login-title">Selamat datang kembali</h2>
                    <p class="login-form-description">Masuk ke akun Nextask Anda untuk melanjutkan.</p>

                    <form class="login-form">
                        <label class="login-field" for="login-identifier">
                            <span>{{ $role === 'siswa' ? 'NISN' : 'Email' }}</span>
                            <input
                                id="login-identifier"
                                name="{{ $role === 'siswa' ? 'nisn' : 'email' }}"
                                type="{{ $role === 'siswa' ? 'text' : 'email' }}"
                                placeholder="{{ $role === 'siswa' ? 'Masukkan NISN Anda' : 'nama@sekolah.sch.id' }}"
                                autocomplete="{{ $role === 'siswa' ? 'username' : 'email' }}"
                                @if ($role === 'siswa') inputmode="numeric" maxlength="10" @endif
                                required
                            >
                        </label>

                        <label class="login-field" for="login-password">
                            <span>Kata Sandi</span>
                            <span class="login-password-wrap">
                                <input
                                    id="login-password"
                                    name="password"
                                    type="password"
                                    placeholder="Masukkan kata sandi"
                                    autocomplete="current-password"
                                    required
                                >
                                <button
                                    class="login-password-toggle"
                                    type="button"
                                    aria-label="Tampilkan kata sandi"
                                    aria-pressed="false"
                                    data-password-toggle
                                >
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                            </span>
                        </label>

                        <button class="login-submit" type="button" disabled>Masuk ke Akun</button>
                    </form>

                    <p class="login-setup-note">Form login sudah disiapkan. Proses autentikasi akan dihubungkan setelah data akun tersedia.</p>
                    <p class="login-switch-role">
                        <a href="{{ route('onboarding') }}">Kembali ke pilihan peran</a>
                    </p>
                </div>
            </section>
        </main>
    </body>
</html>
