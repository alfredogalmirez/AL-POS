<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierTransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\EnsureUserIsCashier;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CreateCashierController;



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::middleware('guest')->group(function () {
//     Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
//     Route::post('/login', [AuthController::class, 'login']);
// });

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth', EnsureUserIsCashier::class)->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/add/{product}', [PosController::class, 'addToCart'])->name('pos.addToCart');
    Route::post('/pos/quantity/update', [PosController::class, 'updateQuantity'])->name('pos.updateQuantity');
    Route::post('/pos/quantity/set', [PosController::class, 'setQuantity'])->name('pos.setQuantity');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::delete('/pos/cart/{product}', [PosController::class, 'deleteFromCart'])->name('pos.remove');

    Route::get('/pos/transactions', [CashierTransactionController::class, 'history'])->name('pos.history');
    Route::post('/post/transaction/{order}/void', [CashierTransactionController::class, 'voidOrder'])->name('pos.transaction.void');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    Route::resource('products', ProductController::class);

    Route::get('/categories/add', [CategoryController::class, 'index'])->name('categories');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}/edit', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'delete'])->name('categories.delete');

    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transaction', [TransactionController::class, 'export'])->name('transactions.export');

    // Cashier Account Creation
    Route::get('/add/cashier', [CreateCashierController::class, 'create'])->name('cashier.create');
    Route::post('/add/cashier', [CreateCashierController::class, 'store'])->name('cashier.store');
});

require __DIR__ . '/auth.php';
