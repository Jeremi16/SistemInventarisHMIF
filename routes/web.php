<?php

use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemTransactionController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
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

    if (Schema::hasColumn('users', 'is_active') && ! $user->is_active) {
        return back()
            ->withErrors(['identifier' => 'Akun ini sedang nonaktif. Hubungi admin HMIF.'])
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

Route::middleware('auth')->group(function () use ($adminDashboard) {
    Route::get('/dashboard', $adminDashboard)->name('dashboard');
    Route::get('/member/dashboard', MemberDashboardController::class)->name('member.dashboard');
});

// Placeholder routes for sidebar navigation
Route::get('/catalog', [ItemController::class, 'catalog'])->middleware('auth')->name('catalog.index');

// Inventory - CRUD: admin & operator only
Route::middleware(['auth', 'role:admin,operator'])->group(function () {
    Route::get('/inventory/create', [ItemController::class, 'create'])->name('inventory.create');
    Route::post('/inventory', [ItemController::class, 'store'])->name('inventory.store');
    Route::get('/inventory/{item}/edit', [ItemController::class, 'edit'])->name('inventory.edit');
    Route::put('/inventory/{item}', [ItemController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{item}', [ItemController::class, 'destroy'])->name('inventory.destroy');
});

// Inventory - View: semua authenticated user
Route::middleware('auth')->group(function () {
    Route::get('/inventory', [ItemController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/{item}', [ItemController::class, 'show'])->name('inventory.show');
    Route::get('/inventory/{item}/qrcode', [ItemController::class, 'qrCode'])->name('inventory.qrcode');
    Route::get('/inventory/{item}/qrcode/download', [ItemController::class, 'downloadQrCode'])->name('inventory.qrcode.download');
});

Route::prefix('incoming')->middleware(['auth', 'role:admin,operator'])->name('incoming.')->group(function () {
    Route::get('/', [ItemTransactionController::class, 'incomingIndex'])->name('index');
    Route::get('/create', [ItemTransactionController::class, 'incomingCreate'])->name('create');
    Route::post('/', [ItemTransactionController::class, 'incomingStore'])->name('store');
});

Route::prefix('outgoing')->middleware(['auth', 'role:admin,operator'])->name('outgoing.')->group(function () {
    Route::get('/', [ItemTransactionController::class, 'outgoingIndex'])->name('index');
    Route::get('/create', [ItemTransactionController::class, 'outgoingCreate'])->name('create');
    Route::post('/', [ItemTransactionController::class, 'outgoingStore'])->name('store');
});

// Borrowing - Request & View: semua authenticated user
Route::prefix('borrowing')->middleware('auth')->name('borrowing.')->group(function () {
    Route::get('/', [BorrowingController::class, 'index'])->name('index');
    Route::get('/request', [BorrowingController::class, 'create'])->name('request');
    Route::post('/request', [BorrowingController::class, 'store'])->name('store');
    Route::get('/{borrowing}', [BorrowingController::class, 'show'])->name('show');
    Route::get('/{borrowing}/extension', [BorrowingController::class, 'extensionForm'])->name('extension');
    Route::post('/{borrowing}/extension', [BorrowingController::class, 'requestExtension'])->name('extension.request');
});

// Borrowing - Approval & Management: admin & operator only
Route::prefix('borrowing')->middleware(['auth', 'role:admin,operator'])->name('borrowing.')->group(function () {
    Route::patch('/{borrowing}/status', [BorrowingController::class, 'updateStatus'])->name('status.update');
    Route::get('/{borrowing}/handover', [BorrowingController::class, 'handoverForm'])->name('handover');
    Route::post('/{borrowing}/handover', [BorrowingController::class, 'recordHandover'])->name('handover.store');
    Route::get('/{borrowing}/return', [BorrowingController::class, 'returnForm'])->name('return');
    Route::post('/{borrowing}/return', [BorrowingController::class, 'recordReturn'])->name('return.store');
    Route::patch('/{borrowing}/extension/approve', [BorrowingController::class, 'approveExtension'])->name('extension.approve');
    Route::patch('/{borrowing}/extension/reject', [BorrowingController::class, 'rejectExtension'])->name('extension.reject');
    Route::post('/{borrowing}/notes', [BorrowingController::class, 'storeNote'])->name('notes.store');
    Route::delete('/notes/{note}', [BorrowingController::class, 'deleteNote'])->name('notes.delete');
    Route::post('/{borrowing}/pre-return', [BorrowingController::class, 'preReturnCheck'])->name('pre-return');
});

Route::prefix('reports')->middleware(['auth', 'role:admin,operator'])->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/stock', [ReportController::class, 'stockReport'])->name('stock');
    Route::get('/borrowing', [ReportController::class, 'borrowingReport'])->name('borrowing');
    Route::get('/condition', [ReportController::class, 'conditionReport'])->name('condition');
    Route::get('/history', [ReportController::class, 'transactionHistory'])->name('history');
    Route::get('/stock/pdf', [ReportController::class, 'exportStockPdf'])->name('stock.pdf');
    Route::get('/borrowing/pdf', [ReportController::class, 'exportBorrowingPdf'])->name('borrowing.pdf');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

// User Management - admin only
Route::middleware(['auth', 'role:admin'])->prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
});
