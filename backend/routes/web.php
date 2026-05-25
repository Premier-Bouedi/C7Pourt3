<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('collection'));
Route::get('/collection', [ProductController::class, 'collection'])->name('collection');
Route::get('/panier', [CartController::class, 'index'])->name('panier');
