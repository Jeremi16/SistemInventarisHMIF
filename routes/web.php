<?php

use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MemberDashboardController;
use App\Models\Borrowing;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
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

    Auth::login($user, $request->boolean('remember'));
    $request->session()->regenerate();
    $request->session()->put('user', [
        'name' => $user->name,
        'nim' => $user->nim ?? null,
        'role' => $user->role ?? 'member',
    ]);

    $dashboardRoute = in_array(strtolower((string) $user->role), ['admin', 'operator'], true)
        ? 'dashboard'
        : 'member.dashboard';

    return redirect()->route($dashboardRoute);
})->name('login.attempt');

$adminDashboard = function (Request $request) {
    $role = strtolower((string) (
        $request->user()?->role
        ?? data_get($request->session()->get('user', []), 'role')
    ));

    if ($role === 'member') {
        return redirect()->route('member.dashboard');
    }

    $recentBorrowings = Borrowing::query()
        ->latest()
        ->take(5)
        ->get();

    $dashboardStats = [
        'total_items' => Item::query()->sum('quantity'),
        'borrowed_items' => Borrowing::query()
            ->whereIn('status', ['approved', 'borrowed', 'overdue'])
            ->count(),
        'maintenance_items' => Item::query()
            ->where('status', 'maintenance')
            ->count(),
        'overdue_borrowings' => Borrowing::query()
            ->where('status', 'overdue')
            ->count(),
    ];

    return view('dashboard.index', compact('recentBorrowings', 'dashboardStats'));
};

Route::get('/dashboard', $adminDashboard)->name('dashboard');

Route::get('/member/dashboard', MemberDashboardController::class)->name('member.dashboard');

// Placeholder routes for sidebar navigation
Route::get('/catalog', [ItemController::class, 'catalog'])->name('catalog.index');
Route::get('/inventory', [ItemController::class, 'index'])->name('inventory.index');
Route::get('/inventory/create', [ItemController::class, 'create'])->name('inventory.create');
Route::post('/inventory', [ItemController::class, 'store'])->name('inventory.store');
Route::get('/inventory/{item}', [ItemController::class, 'show'])->name('inventory.show');

Route::prefix('incoming')->name('incoming.')->group(function () use ($adminDashboard) {
    Route::get('/', $adminDashboard)->name('index');
});

Route::prefix('outgoing')->name('outgoing.')->group(function () use ($adminDashboard) {
    Route::get('/', $adminDashboard)->name('index');
});

Route::prefix('borrowing')->name('borrowing.')->group(function () {
    Route::get('/', [BorrowingController::class, 'index'])->name('index');
    Route::get('/request', [BorrowingController::class, 'create'])->name('request');
    Route::post('/request', [BorrowingController::class, 'store'])->name('store');
    Route::get('/{borrowing}', [BorrowingController::class, 'show'])->name('show');
    Route::patch('/{borrowing}/status', [BorrowingController::class, 'updateStatus'])->name('status.update');
});

Route::prefix('reports')->name('reports.')->group(function () use ($adminDashboard) {
    Route::get('/', $adminDashboard)->name('index');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');
