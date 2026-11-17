<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard - Nextask</title>
    </head>
    <body>
        <main>
            <h1>Selamat datang, {{ auth()->user()->name }}</h1>
            <p>Dashboard Nextask sedang dalam pengembangan.</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Keluar</button>
            </form>
        </main>
    </body>
</html>
