<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#f7f8fa">
        <title>Mulai bersama Nextask</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="onboarding-page font-sans antialiased">
        <main class="onboarding-shell">
            <a class="onboarding-brand" href="{{ route('onboarding') }}" aria-label="Nextask, beranda">
                <span class="onboarding-brand-mark" aria-hidden="true">N</span>
                <span>Nextask</span>
            </a>

            <section class="onboarding-intro" aria-labelledby="onboarding-title">
                <h1 id="onboarding-title">Kuasai Alur Kerja Anda</h1>
                <p>Platform terintegrasi untuk mengelola tugas dan aktivitas belajar di sekolah.</p>
            </section>

            <form class="onboarding-form" action="/login/siswa" method="GET" data-onboarding-form>
                <fieldset class="border-0 p-0">
                    <legend class="onboarding-legend">Pilih peran Anda</legend>
                    <div class="onboarding-roles">
                        <label>
                            <input
                                class="onboarding-role-input"
                                type="radio"
                                name="role"
                                value="siswa"
                                data-login-path="/login/siswa"
                            >
                            <span class="onboarding-role-card">
                                <span class="onboarding-role-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m3 9 9-5 9 5-9 5-9-5Z"/>
                                        <path d="M7 11.2v4.3c2.8 2.2 7.2 2.2 10 0v-4.3"/>
                                        <path d="M21 9v5"/>
                                    </svg>
                                </span>
                                <span class="onboarding-role-copy">
                                    <strong>Siswa</strong>
                                    <small>Pelajar Sekolah</small>
                                </span>
                            </span>
                        </label>

                        <label>
                            <input
                                class="onboarding-role-input"
                                type="radio"
                                name="role"
                                value="guru"
                                data-login-path="/login/guru"
                            >
                            <span class="onboarding-role-card">
                                <span class="onboarding-role-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 5h16v11H4z"/>
                                        <path d="M8 20h8M12 16v4"/>
                                        <path d="m8 9 2 2 4-4"/>
                                    </svg>
                                </span>
                                <span class="onboarding-role-copy">
                                    <strong>Guru</strong>
                                    <small>Pengajar Sekolah</small>
                                </span>
                            </span>
                        </label>
                    </div>
                </fieldset>

                <button class="onboarding-submit" type="submit" disabled>Mulai Sekarang</button>
            </form>
        </main>
    </body>
</html>
