<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with key metrics
     */
    public function index(): Response
    {
        // Global Statistics
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalRevenue = Order::where('status', 'delivered')->sum('total');
        $activeProducts = Product::where('is_active', true)->count();

        // Stock status by location
        $casablancaStock = Product::where('is_active', true)->sum('stock_casablanca') ?? 0;
        $librevilleStock = Product::where('is_active', true)->sum('stock_libreville') ?? 0;

        // 48-hour transit alerts
        $transitAlerts = Order::where('status', 'in_transit')
            ->where('updated_at', '>=', now()->subHours(48))
            ->count();

        // Return dashboard data
        $dashboardData = [
            'stats' => [
                'total_orders' => $totalOrders,
                'pending_orders' => $pendingOrders,
                'total_revenue' => $totalRevenue,
                'active_products' => $activeProducts,
                'currency' => 'MAD',
            ],
            'stock' => [
                'casablanca' => $casablancaStock,
                'libreville' => $librevilleStock,
            ],
            'alerts' => [
                'transit_48h' => $transitAlerts,
            ],
        ];
        return Inertia::render('Dashboard', $dashboardData);
    }

    /**
     * Display stock overview page.
     */
    public function stock(): Response
    {
        // Stock status by location
        $casablancaStock = Product::where('is_active', true)->sum('stock_casablanca') ?? 0;
        $librevilleStock = Product::where('is_active', true)->sum('stock_libreville') ?? 0;

        // Reuse the same stock data as dashboard
        $stockData = [
            'stock' => [
                'casablanca' => $casablancaStock,
                'libreville' => $librevilleStock,
            ],
        ];
        return Inertia::render('Admin/Stock', $stockData);
    }
}
