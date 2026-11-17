<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(Request $request): View
    {
        $role = $request->query('role', old('login_role', 'guru'));

        return view('auth.login', [
            'role' => is_string($role) ? $role : 'guru',
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $loginRole = $request->input('login_role', 'guru');

        if (! is_string($loginRole)) {
            $loginRole = 'guru';
        }

        $isStudentLogin = $loginRole === 'siswa';
        $identifierField = $isStudentLogin ? 'nisn' : 'email';

        $credentials = $request->validate([
            $identifierField => $isStudentLogin
                ? ['required', 'string']
                : ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        $user = $isStudentLogin
            ? $this->findStudentByNisn($credentials['nisn'])
            : $this->findStaffByEmail($credentials['email']);

        if (! $user || ! Auth::attempt([
            'email' => $user->email,
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    $identifierField => $isStudentLogin
                        ? 'NISN atau kata sandi salah.'
                        : 'Email atau kata sandi salah.',
                ])
                ->withInput([
                    'login_role' => $loginRole,
                    $identifierField => $credentials[$identifierField],
                    'remember' => $request->boolean('remember'),
                ]);
        }

        $request->session()->regenerate();

        $dashboardRoutes = [
            'admin' => 'admin.dashboard',
            'guru' => 'guru.dashboard',
            'siswa' => 'siswa.dashboard',
        ];
        $roleName = $request->user()->role->name;

        if (! array_key_exists($roleName, $dashboardRoutes)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            abort(403);
        }

        return redirect()->route($dashboardRoutes[$roleName]);
    }

    private function findStudentByNisn(string $nisn): ?User
    {
        return User::query()
            ->whereHas('role', fn (Builder $query) => $query->where('name', 'siswa'))
            ->whereHas('siswaProfile', fn (Builder $query) => $query->where('nisn', $nisn))
            ->first();
    }

    private function findStaffByEmail(string $email): ?User
    {
        return User::query()
            ->where('email', $email)
            ->whereHas('role', fn (Builder $query) => $query->whereIn('name', ['guru', 'admin']))
            ->first();
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
