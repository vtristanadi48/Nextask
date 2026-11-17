<?php

use App\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.onboarding');
})->name('onboarding');

Route::get('/login/{role}', [AuthenticatedSessionController::class, 'create'])
    ->whereIn('role', ['siswa', 'guru'])
    ->name('login');

Route::post('/login/{role}', [AuthenticatedSessionController::class, 'store'])
    ->middleware('throttle:5,1')
    ->whereIn('role', ['siswa', 'guru'])
    ->name('login.store');

Route::view('/dashboard', 'pages.dashboard')
    ->middleware('auth')
    ->name('dashboard');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
