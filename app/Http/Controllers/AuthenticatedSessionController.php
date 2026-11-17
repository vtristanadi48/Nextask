<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(string $role): View
    {
        return view('pages.auth.login', [
            'role' => $role,
        ]);
    }

    public function store(Request $request, string $role): RedirectResponse
    {
        $identifierField = $role === 'siswa' ? 'nisn' : 'email';
        $identifierRules = $role === 'siswa'
            ? ['required', 'string', 'digits:10']
            : ['required', 'string', 'email'];

        $credentials = $request->validate([
            $identifierField => $identifierRules,
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([
            $identifierField => $credentials[$identifierField],
            'password' => $credentials['password'],
            'role' => $role,
        ])) {
            return back()
                ->withErrors([
                    'credentials' => 'NISN/email atau kata sandi yang Anda masukkan salah.',
                ])
                ->onlyInput($identifierField);
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('onboarding');
    }
}
