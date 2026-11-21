<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $roleCounts = Role::query()
            ->whereIn('name', ['siswa', 'guru'])
            ->withCount('users')
            ->pluck('users_count', 'name');

        return view('admin.dashboard', [
            'totalStudents' => $roleCounts->get('siswa', 0),
            'totalTeachers' => $roleCounts->get('guru', 0),
            'totalClasses' => Schema::hasTable('kelas') ? DB::table('kelas')->count() : 0,
            'totalUsers' => User::query()->count(),
            'recentUsers' => User::query()
                ->with('role')
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
