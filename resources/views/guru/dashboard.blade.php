@extends('layouts.dashboard')

@section('title', 'Dashboard Guru - Nextask')

@section('content')
    <section class="mx-auto max-w-5xl rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">Dashboard Guru</p>
        <h1 class="mt-3 text-3xl font-semibold">Selamat datang, {{ auth()->user()->name }}</h1>
        <p class="mt-2 text-slate-600">Role: {{ auth()->user()->role->name }}</p>
        <p class="mt-6 text-slate-600">Dashboard Nextask sedang dalam pengembangan.</p>
    </section>
@endsection
