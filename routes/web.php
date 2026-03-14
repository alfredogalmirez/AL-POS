<?php

use App\Http\Controllers\PosController;
use Illuminate\Support\Facades\Route;

Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
Route::post('/pos/add/{product}', [PosController::class, 'addToCart'])->name('pos.addToCart');
