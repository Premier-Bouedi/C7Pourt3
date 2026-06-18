<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ============================================================================
// PUBLIC ROUTES - Site Vitrine C7Pourt3 (Client-facing)
// ============================================================================

// Homepage redirect
Route::get('/', fn () => redirect()->route('collection'));

// Product collection & catalog
Route::get('/collection', [ProductController::class, 'collection'])->name('collection');

// Authentication (public access)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Shopping cart & checkout
Route::get('/panier', [CartController::class, 'index'])->name('panier');
Route::get('/commander', [OrderController::class, 'checkout'])->name('commander');
Route::get('/commande/{reference}', [OrderController::class, 'confirmation'])->name('commande.confirmation');

// Customer order tracking & reviews
Route::get('/suivi', [OrderController::class, 'track'])->name('suivi');
Route::get('/avis', [OrderController::class, 'reviewsPage'])->name('avis');

// Info pages
Route::get('/infos', fn () => Inertia::render('Infos'))->name('infos');

// ============================================================================
// PRIVATE ROUTES - Administration Panel (Template: Bleu Nuit Luxe)
// Protected by 'auth' middleware
// ============================================================================

Route::middleware('auth')->group(function () {
    // Dashboard & Analytics
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Newsletter Management
    Route::get('/newsletter', [NewsletterController::class, 'index'])->name('newsletter.index');

    // Orders Management
    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders', 'index')->name('orders.index');
        Route::get('/orders/{id}', 'show')->name('orders.show');
    });

    // Payments COD (Cash On Delivery)
    Route::get('/payments-cod', [PaymentController::class, 'index'])->name('payments.cod.index');

    // Products CRUD (complete resource)
    Route::resource('products', ProductController::class)->except(['collection', 'index']);
    Route::get('/products', [ProductController::class, 'manage'])->name('products.manage');

    // Reviews Management
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
});
