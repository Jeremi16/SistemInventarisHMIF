<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class AuthSession
{
    public static function login(Request $request, User $user, bool $remember = false): RedirectResponse
    {
        Auth::login($user, $remember);
        $request->session()->regenerate();
        $request->session()->put('user', [
            'name' => $user->name,
            'nim' => $user->nim ?? null,
            'role' => $user->role ?? 'member',
        ]);

        return redirect()->route(self::dashboardRoute($user));
    }

    public static function dashboardRoute(User $user): string
    {
        return in_array(strtolower((string) $user->role), ['admin', 'operator'], true)
            ? 'dashboard'
            : 'member.dashboard';
    }
}
