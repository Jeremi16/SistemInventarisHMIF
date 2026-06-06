<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuthSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'identifier.required' => 'Email atau NIM wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $identifier = $credentials['identifier'];
        $userQuery = User::query()->where('email', $identifier);

        if (Schema::hasColumn('users', 'nim')) {
            $userQuery->orWhere('nim', $identifier);
        }

        $user = $userQuery->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withErrors(['identifier' => 'Email/NIM atau password tidak sesuai.'])
                ->onlyInput('identifier');
        }

        if (Schema::hasColumn('users', 'is_active') && ! $user->is_active) {
            return back()
                ->withErrors(['identifier' => 'Akun ini sedang nonaktif. Hubungi admin HMIF.'])
                ->onlyInput('identifier');
        }

        return AuthSession::login($request, $user, $request->boolean('remember'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
