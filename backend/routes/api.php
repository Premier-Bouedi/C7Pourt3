<?php

use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/products/{product}/quick-view', [ProductController::class, 'quickView']);
Route::get('/products/{product}/reviews', [ReviewController::class, 'forProduct']);

Route::post('/newsletter', [NewsletterController::class, 'subscribe']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/reviewable', [ReviewController::class, 'reviewableOrder']);
Route::post('/reviews', [ReviewController::class, 'store']);
