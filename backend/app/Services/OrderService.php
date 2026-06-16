<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Mail\OrderConfirmedMail;
use App\Mail\OrderDeliveredMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private readonly WhatsAppService $whatsApp,
    ) {}

    /**
     * @param  array<int, array{variant_id: int, quantity: int}>  $cartItems
     * @param  array<string, mixed>  $customer
     */
    public function createFromCart(array $cartItems, array $customer): Order
    {
        if (empty($cartItems)) {
            throw ValidationException::withMessages(['items' => 'Le panier est vide.']);
        }

        return DB::transaction(function () use ($cartItems, $customer) {
            $subtotal = 0;
            $lineSnapshots = [];

            foreach ($cartItems as $item) {
                $variant = ProductVariant::with('product')->findOrFail($item['variant_id']);
                if (! $variant->isInStock()) {
                    throw ValidationException::withMessages([
                        'items' => "Rupture de stock pour {$variant->product->name}.",
                    ]);
                }
                $qty = max(1, (int) $item['quantity']);
                $unit = $variant->effectivePrice();
                $lineTotal = $unit * $qty;
                $subtotal += $lineTotal;
                $lineSnapshots[] = compact('variant', 'qty', 'unit', 'lineTotal');
            }

            $shipping = (int) config('c7pourt3.shipping_fee', 5000);
            $total = $subtotal + $shipping;

            $order = Order::create([
                'reference' => 'C7-'.strtoupper(Str::random(8)),
                'customer_name' => $customer['name'],
                'customer_email' => $customer['email'] ?? null,
                'customer_phone' => $customer['phone'],
                'shipping_city' => $customer['city'],
                'shipping_address' => $customer['address'],
                'notes' => $customer['notes'] ?? null,
                'status' => OrderStatus::Pending,
                'subtotal' => $subtotal,
                'shipping_fee' => $shipping,
                'total' => $total,
                'currency' => 'XAF',
                'estimated_delivery_at' => now()->addDays(8),
            ]);

            foreach ($lineSnapshots as $row) {
                $v = $row['variant'];
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $v->id,
                    'product_id' => $v->product_id,
                    'product_name' => $v->product->name,
                    'variant_color' => $v->color,
                    'sku' => $v->sku,
                    'unit_price' => $row['unit'],
                    'quantity' => $row['qty'],
                    'line_total' => $row['lineTotal'],
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'amount_due' => $total,
                'amount_collected' => 0,
                'payment_method' => 'cod_cash',
                'status' => PaymentStatus::Pending,
            ]);

            $order->load('items');

            if ($order->customer_email) {
                Mail::to($order->customer_email)->send(new OrderConfirmedMail($order));
            }

            return $order;
        });
    }

    public function markDelivered(Order $order): Order
    {
        if ($order->status === OrderStatus::Delivered && $order->delivered_at) {
            return $order;
        }

        $order->update([
            'status' => OrderStatus::Delivered,
            'delivered_at' => $order->delivered_at ?? now(),
        ]);

        $comm = app(OrderCommunicationService::class);

        if ($order->customer_email) {
            Mail::to($order->customer_email)->send(new OrderDeliveredMail($order));
        }

        $comm->sendReviewRequestPack($order);

        return $order;
    }

    public function whatsApp(): WhatsAppService
    {
        return $this->whatsApp;
    }
}
