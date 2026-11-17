<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard Admin - Nextask</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
        <main class="mx-auto max-w-5xl px-6 py-12">
            <section class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">Dashboard Admin</p>
                <h1 class="mt-3 text-3xl font-semibold">Selamat datang, {{ auth()->user()->name }}</h1>
                <p class="mt-2 text-slate-600">Role: {{ auth()->user()->role->name }}</p>
                <p class="mt-6 text-slate-600">Dashboard Nextask sedang dalam pengembangan.</p>
                <form class="mt-8" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white hover:bg-blue-800" type="submit">Logout</button>
                </form>
            </section>
        </main>
    </body>
</html>
