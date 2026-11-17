<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#f5f8fc">
        <title>Masuk - Nextask</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="login-page font-sans antialiased">
        <main class="login-layout">
            <section class="login-brand-panel" aria-label="Tentang Nextask">
                <a class="login-brand" href="{{ route('onboarding') }}" aria-label="Nextask, pilih peran">
                    <img class="login-brand-logo" src="{{ asset('images/brand/logo-teks-kanan.svg') }}" alt="">
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
                        <span class="login-mobile-mark" aria-hidden="true">
                            <img src="{{ asset('images/brand/logo.svg') }}" alt="">
                        </span>
                        <span>Nextask</span>
                    </a>

                    <span class="login-role-badge">
                        {{ $role === 'siswa'
                            ? 'Akun Siswa'
                            : 'Akun Guru & Admin' }}
                    </span>
                    <h2 id="login-title">Selamat datang kembali</h2>
                    <p class="login-form-description">
                        {{ $role === 'siswa'
                            ? 'Masukkan NISN dan kata sandi untuk mulai belajar dan mengerjakan tugas.'
                            : 'Masukkan email dan kata sandi untuk mengelola kelas dan tugas.' }}
                    </p>

                    <form class="login-form" method="POST" action="{{ route('login.post') }}">
                        @csrf
                        <input type="hidden" name="login_role" value="{{ $role }}">

                        @if ($role === 'siswa')
                            <label class="login-field" for="login-identifier">
                                <span>NISN</span>
                                <input
                                    id="login-identifier"
                                    name="nisn"
                                    type="text"
                                    inputmode="numeric"
                                    placeholder="Masukkan NISN Anda"
                                    autocomplete="username"
                                    value="{{ old('nisn') }}"
                                    @if ($errors->has('nisn')) aria-invalid="true" @endif
                                    required
                                >
                                @error('nisn')
                                    <span class="login-error" role="alert">{{ $message }}</span>
                                @enderror
                            </label>
                        @else
                            <label class="login-field" for="login-identifier">
                                <span>Email</span>
                                <input
                                    id="login-identifier"
                                    name="email"
                                    type="email"
                                    placeholder="Masukkan email Anda"
                                    autocomplete="username"
                                    value="{{ old('email') }}"
                                    @if ($errors->has('email')) aria-invalid="true" @endif
                                    required
                                >
                                @error('email')
                                    <span class="login-error" role="alert">{{ $message }}</span>
                                @enderror
                            </label>
                        @endif

                        <label class="login-field" for="login-password">
                            <span>Kata Sandi</span>
                            <span class="login-password-wrap">
                                <input
                                    id="login-password"
                                    name="password"
                                    type="password"
                                    placeholder="Masukkan kata sandi"
                                    autocomplete="current-password"
                                    @if ($errors->has('password')) aria-invalid="true" @endif
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
                            @error('password')
                                <span class="login-error" role="alert">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="login-remember">
                            <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                            <span>Ingat saya</span>
                        </label>

                        <button class="login-submit" type="submit">Masuk ke Akun</button>
                    </form>

                    <p class="login-switch-role">
                        <a href="{{ route('onboarding') }}">Kembali ke pilihan peran</a>
                    </p>
                </div>
            </section>
        </main>
    </body>
</html>
