<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;

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

Route::prefix('admin')->name('admin.')->group(function () {
    // Root admin URL redirects to dashboard
    Route::get('/', function () { return redirect()->route('admin.dashboard'); })->name('home');
    // Dashboard & Analytics
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Routes de la Top Navbar (Clients, Stock, Factures, Paramètres)
    Route::get('/clients', function () { return inertia('Admin/Clients'); })->name('clients.index');
    Route::get('/stock', [DashboardController::class, 'stock'])->name('stock.index');
    Route::get('/invoices', function () { return inertia('Admin/Invoices'); })->name('invoices.index');
    Route::get('/settings', function () { return inertia('Admin/Settings'); })->name('settings');

    // Bot IA Assistant
    Route::post('/ai-assistant/ask', [\App\Http\Controllers\Admin\AiAssistantController::class, 'ask'])->name('ai.ask');

    // Newsletter Management (plural route)
    Route::get('/newsletters', [NewsletterController::class, 'index'])->name('newsletters.index');

    // Orders Management
    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders', 'index')->name('orders.index');
        Route::get('/orders/{id}', 'show')->name('orders.show');
    });

    // Payments COD (Cash On Delivery) - plural route
    Route::get('/cod-payments', [PaymentController::class, 'index'])->name('cod-payments.index');

    // Products CRUD (complete resource)
    Route::resource('products', ProductController::class)->except(['collection', 'index']);
    Route::get('/products', [ProductController::class, 'manage'])->name('products.manage');

    // Reviews Management
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
});

    // Register Filament admin panel routes
    // Filament routes need to be loaded within a middleware group (web + auth)
    // This block registers Filament's own admin UI routes.
    // Filament routes registration has been disabled because the
    // current Filament installation does not provide a `routes()` method.
    // If you install a compatible Filament version, re‑enable the line below:
    // \Filament\Facades\Filament::routes();

Route::get('/admin/orders-filament', [App\Http\Controllers\OrderController::class, 'index'])->name('filament.admin.resources.orders.index');
Route::get('/admin/products-filament', [App\Http\Controllers\ProductController::class, 'manage'])->name('filament.admin.resources.products.index');
Route::get('/admin/reviews-filament', [App\Http\Controllers\ReviewController::class, 'index'])->name('filament.admin.resources.reviews.index');
