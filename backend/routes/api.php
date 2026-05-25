<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/products/{product}/quick-view', [ProductController::class, 'quickView']);
