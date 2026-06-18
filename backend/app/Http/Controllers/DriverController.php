<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * GET /api/driver/orders
     *
     * Récupère les commandes à Casablanca dont le statut est 'en_cours_de_livraison'.
     * Calcule via Carbon le temps restant avant la deadline de 48h.
     */
    public function orders(): JsonResponse
    {
        $orders = Order::with(['items.product', 'items.variant', 'payment'])
            ->where('status', OrderStatus::EnCoursLivraison)
            ->where('shipping_city', 'Casablanca')
            ->orderBy('created_at', 'asc')
            ->get();

        $data = $orders->map(function (Order $order) {
            // Deadline = 48h après la confirmation ou la création
            $startTime = $order->confirmed_at ?? $order->created_at;
            $deadline = Carbon::parse($startTime)->addHours(48);
            $now = Carbon::now();

            $remainingMinutes = max(0, $now->diffInMinutes($deadline, false));
            $remainingHours = floor($remainingMinutes / 60);
            $remainingMins = $remainingMinutes % 60;

            $isExpired = $now->greaterThanOrEqualTo($deadline);

            return [
                'id' => $order->id,
                'reference' => $order->reference,
                'status' => $order->status->value,
                'status_label' => $order->status->label(),

                // Informations client
                'customer' => [
                    'name' => $order->customer_name,
                    'phone' => $order->customer_phone,
                    'address' => $order->shipping_address,
                    'city' => $order->shipping_city,
                ],

                // Détails commande
                'items' => $order->items->map(fn ($item) => [
                    'id' => $item->id,
                    'product_name' => $item->product_name,
                    'variant_color' => $item->variant_color,
                    'sku' => $item->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->line_total,
                ]),

                // Montants
                'subtotal' => $order->subtotal,
                'shipping_fee' => $order->shipping_fee,
                'total' => $order->total,
                'currency' => $order->currency ?? 'MAD',

                // Paiement COD
                'payment' => $order->payment ? [
                    'method' => $order->payment->payment_method,
                    'status' => $order->payment->status->value,
                    'amount_due' => $order->payment->amount_due,
                ] : null,

                // Deadline 48h
                'deadline' => [
                    'expires_at' => $deadline->toIso8601String(),
                    'remaining_hours' => (int) $remainingHours,
                    'remaining_minutes' => (int) $remainingMins,
                    'remaining_total_minutes' => (int) $remainingMinutes,
                    'is_expired' => $isExpired,
                    'display' => $isExpired
                        ? 'EXPIRÉ'
                        : sprintf('%dh %02dmin restantes', $remainingHours, $remainingMins),
                ],

                'created_at' => $order->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $data->count(),
            'orders' => $data,
        ]);
    }

    /**
     * POST /api/driver/orders/{id}/complete
     *
     * Valide la livraison d'une commande :
     * 1. Passe le statut à 'livré'
     * 2. Valide le paiement COD
     * 3. Décrémente le stock de l'entrepôt Casablanca (stock_morocco sur Product)
     */
    public function complete(Request $request, int $id): JsonResponse
    {
        $order = Order::with(['items.variant', 'items.product', 'payment'])
            ->where('status', OrderStatus::EnCoursLivraison)
            ->findOrFail($id);

        // Vérifier que la commande est bien à Casablanca
        if (strtolower($order->shipping_city) !== 'casablanca') {
            return response()->json([
                'success' => false,
                'message' => 'Cette commande n\'est pas assignée à Casablanca.',
            ], 403);
        }

        // 1. Passer le statut à "livré"
        $order->update([
            'status' => OrderStatus::Delivered,
            'delivered_at' => Carbon::now(),
        ]);

        // 2. Valider le paiement COD
        if ($order->payment) {
            $order->payment->update([
                'status' => PaymentStatus::Collected,
                'amount_collected' => $order->payment->amount_due,
                'collected_at' => Carbon::now(),
            ]);
        }

        // 3. Décrémenter le stock de l'entrepôt Casablanca
        foreach ($order->items as $item) {
            // Décrémenter stock_morocco sur le Product
            if ($item->product) {
                $item->product->decrement('stock_morocco', $item->quantity);
            }

            // Décrémenter aussi le stock de la variante si elle existe
            if ($item->variant) {
                $item->variant->decrement('stock', $item->quantity);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Livraison validée avec succès.',
            'order' => [
                'id' => $order->id,
                'reference' => $order->reference,
                'status' => OrderStatus::Delivered->value,
                'status_label' => OrderStatus::Delivered->label(),
                'delivered_at' => $order->delivered_at->toIso8601String(),
                'payment_status' => $order->payment?->status->value ?? 'N/A',
            ],
        ]);
    }
}
