<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.onboarding');
})->name('onboarding');

Route::get('/login/{role}', function (string $role) {
    return view('pages.auth.login', [
        'role' => $role,
    ]);
})->whereIn('role', ['siswa', 'guru'])->name('login');
