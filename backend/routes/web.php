<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => redirect()->route('collection'));
Route::get('/collection', [ProductController::class, 'collection'])->name('collection');
Route::get('/panier', [CartController::class, 'index'])->name('panier');
Route::get('/commander', [OrderController::class, 'checkout'])->name('commander');
Route::get('/commande/{reference}', [OrderController::class, 'confirmation'])->name('commande.confirmation');
Route::get('/suivi', [OrderController::class, 'track'])->name('suivi');
Route::get('/avis', [OrderController::class, 'reviewsPage'])->name('avis');
Route::get('/infos', fn () => Inertia::render('Infos'))->name('infos');
