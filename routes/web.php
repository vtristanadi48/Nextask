<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminKelasController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.onboarding');
})->name('onboarding');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function () {
            Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
            Route::get('/kelas', [AdminKelasController::class, 'index'])->name('kelas');
            Route::post('/kelas/{tab}', [AdminKelasController::class, 'store'])
                ->whereIn('tab', ['guru', 'siswa', 'kelas', 'jurusan', 'mapel'])
                ->name('kelas.store');
            Route::put('/kelas/{tab}/{id}', [AdminKelasController::class, 'update'])
                ->whereIn('tab', ['guru', 'siswa', 'kelas', 'jurusan', 'mapel'])
                ->whereNumber('id')
                ->name('kelas.update');
            Route::delete('/kelas/{tab}/{id}', [AdminKelasController::class, 'destroy'])
                ->whereIn('tab', ['guru', 'siswa', 'kelas', 'jurusan', 'mapel'])
                ->whereNumber('id')
                ->name('kelas.destroy');
        });

    Route::prefix('guru')
        ->name('guru.')
        ->middleware('role:guru')
        ->group(function () {
            Route::view('/dashboard', 'guru.dashboard')->name('dashboard');
        });

    Route::prefix('siswa')
        ->name('siswa.')
        ->middleware('role:siswa')
        ->group(function () {
            Route::view('/dashboard', 'siswa.dashboard')->name('dashboard');
        });
});
