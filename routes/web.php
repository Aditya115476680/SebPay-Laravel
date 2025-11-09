<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ToppingController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\TransactionController;


Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


Route::get('/toppings', [ToppingController::class, 'index'])->name('toppings.index');
Route::post('/toppings', [ToppingController::class, 'store'])->name('toppings.store');
Route::get('/toppings/{id}/edit', [ToppingController::class, 'edit'])->name('toppings.edit');
Route::put('/toppings/{id}', [ToppingController::class, 'update'])->name('toppings.update');
Route::delete('/toppings/{id}', [ToppingController::class, 'destroy'])->name('toppings.destroy');


Route::get('/topping-in', [MovementController::class, 'inIndex'])->name('topping.in');
Route::post('/topping-in', [MovementController::class, 'storeIn'])->name('topping.in.store');

Route::get('/topping-out', [MovementController::class, 'outIndex'])->name('topping.out');
Route::post('/topping-out', [MovementController::class, 'storeOut'])->name('topping.out.store');


Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
Route::post('/transactions/store', [TransactionController::class, 'store'])->name('transactions.store');
Route::get('/transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');

Route::get('/riwayat-transaksi', [TransactionController::class, 'history'])->name('transactions.history');
Route::get('/struk/{id}', [TransactionController::class, 'receipt'])->name('transactions.receipt');
