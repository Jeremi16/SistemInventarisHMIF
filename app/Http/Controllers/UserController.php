<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private const ROLES = ['admin', 'operator', 'member'];

    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('nim', 'like', '%' . $request->search . '%'))
            ->when($request->filled('role') && $request->role !== 'all', fn ($q) => $q->where('role', $request->role))
            ->latest()
            ->paginate(15);

        return view('users.index', [
            'users' => $users,
            'roles' => self::ROLES,
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'roles' => self::ROLES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'nim' => ['required', 'string', 'max:20', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'string', Rule::in(self::ROLES)],
            'phone' => ['nullable', 'string', 'max:20'],
            'batch' => ['nullable', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'nim.required' => 'NIM wajib diisi.',
            'nim.unique' => 'NIM sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.in' => 'Role tidak valid.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        User::create($validated);

        return redirect()
            ->route('users.index')
            ->with('user_created', 'Pengguna baru berhasil didaftarkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => self::ROLES,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nim' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(self::ROLES)],
            'phone' => ['nullable', 'string', 'max:20'],
            'batch' => ['nullable', 'string', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'nim.required' => 'NIM wajib diisi.',
            'nim.unique' => 'NIM sudah terdaftar.',
            'role.in' => 'Role tidak valid.',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->filled('password')) {
            $request->validate(['password' => ['string', 'min:6']]);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()
            ->route('users.index')
            ->with('user_updated', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->update(['is_active' => false]);

        return redirect()
            ->route('users.index')
            ->with('user_deleted', 'Pengguna berhasil dinonaktifkan.');
    }
}
