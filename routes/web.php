<?php

use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');

// Placeholder routes for sidebar navigation
Route::get('/inventory', [ItemController::class, 'index'])->name('inventory.index');

Route::prefix('incoming')->name('incoming.')->group(function () {
    Route::get('/', function () { return view('dashboard.index'); })->name('index');
});

Route::prefix('outgoing')->name('outgoing.')->group(function () {
    Route::get('/', function () { return view('dashboard.index'); })->name('index');
});

Route::prefix('borrowing')->name('borrowing.')->group(function () {
    Route::get('/', function () { return view('dashboard.index'); })->name('index');
});

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', function () { return view('dashboard.index'); })->name('index');
});

Route::post('/logout', function () {
    return redirect('/');
})->name('logout');
