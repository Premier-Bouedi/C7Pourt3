<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ============================================================================
// MOBILE APP AUTHENTICATION
// ============================================================================

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    if (Auth::attempt($credentials)) {
        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie.',
            'user'    => Auth::user(),
        ], 200);
    }

    return response()->json([
        'success' => false,
        'message' => 'Identifiants invalides.',
    ], 401);
});

// ============================================================================
// PUBLIC API ROUTES - Client Site Integration (C7Pourt3 Showcase)
// ============================================================================

// Product Catalog API
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}/quick-view', [ProductController::class, 'quickView']);
Route::get('/products/{product}/reviews', [ReviewController::class, 'forProduct']);

// Newsletter Subscription
Route::post('/newsletter', [NewsletterController::class, 'subscribe']);

// ============================================================================
// ORDER & WEBHOOK ROUTES - Client to Management System
// ============================================================================

// Submit orders from client site
Route::post('/orders', [OrderController::class, 'store']);

// Webhook endpoint for validated orders from showcase
Route::post('/orders/webhook', [OrderController::class, 'webhook']);

// ============================================================================
// REVIEW ROUTES - Client Feedback
// ============================================================================

Route::get('/orders/reviewable', [ReviewController::class, 'reviewableOrder']);
Route::post('/reviews', [ReviewController::class, 'store']);

// ============================================================================
// DRIVER ROUTES - Application Mobile Flutter (Livreurs Casablanca)
// ============================================================================

Route::prefix('driver')->group(function () {
    // Liste des commandes en cours de livraison à Casablanca (avec deadline 48h)
    Route::get('/orders', [DriverController::class, 'orders']);

    // Valider la livraison d'une commande (statut → livré, COD validé, stock décrémenté)
    Route::post('/orders/{id}/complete', [DriverController::class, 'complete']);
});

// ============================================================================
// AI ROUTES - Module Intelligence Artificielle (Recommandation Luxe)
// ============================================================================

Route::match(['get', 'post'], '/ai/recommend', [AiController::class, 'chat']);

