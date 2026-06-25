<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class AgencyController extends Controller
{
    /**
     * GET /api/agency/dashboard
     *
     * Tableau de bord agence Libreville pour l'application mobile Flutter.
     */
    public function dashboard(): JsonResponse
    {
        $deliveredOrders = Order::with('items')
            ->where('status', OrderStatus::Delivered)
            ->whereRaw('LOWER(shipping_city) = ?', ['libreville'])
            ->orderByDesc('delivered_at')
            ->get();

        $totalDelivered = (int) $deliveredOrders->sum(
            fn (Order $order) => $order->items->sum('quantity')
        );

        $totalRevenueXaf = (int) $deliveredOrders->sum('total');

        $stockAgency = (int) Product::where('is_active', true)->sum('stock_morocco');

        $deliveredHistory = $deliveredOrders->flatMap(function (Order $order) {
            return $order->items->map(fn ($item) => [
                'id' => 'ORD-'.$order->id.'-'.$item->id,
                'label' => $item->product_name,
                'quantity' => (int) $item->quantity,
                'unit_price' => (int) $item->unit_price,
                'shipping_origin' => 'Maroc',
                'status' => 'Livré',
            ]);
        })->values();

        $agencyStock = Product::where('is_active', true)
            ->where('stock_morocco', '>', 0)
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (Product $product) => [
                'id' => 'STK-'.$product->id,
                'label' => $product->name,
                'quantity' => (int) $product->stock_morocco,
                'unit_price' => (int) $product->displayPrice(),
                'shipping_origin' => 'Maroc',
                'status' => 'En attente',
            ])
            ->values();

        return response()->json([
            'success' => true,
            'total_delivered' => $totalDelivered,
            'stock_agency' => $stockAgency,
            'total_revenue_xaf' => $totalRevenueXaf,
            'delivered_history' => $deliveredHistory,
            'agency_stock' => $agencyStock,
        ]);
    }
}
