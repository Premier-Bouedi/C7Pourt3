<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orders,
        private readonly WhatsAppService $whatsApp,
    ) {}

    public function checkout(): Response
    {
        return Inertia::render('Checkout', [
            'shippingFee' => (int) config('c7pourt3.shipping_fee', 5000),
            'currency' => 'MAD',
            'whatsappCheckout' => $this->whatsApp->checkoutHelp(),
        ]);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orders->createFromCart(
            $request->validated('items'),
            $request->validated('customer'),
        );

        return response()->json([
            'order' => [
                'reference' => $order->reference,
                'total' => $order->total,
                'currency' => 'MAD',
                'whatsapp_url' => $this->whatsApp->orderConfirmed($order),
            ],
            'redirect' => route('commande.confirmation', $order->reference),
        ], 201);
    }

    public function confirmation(string $reference): Response
    {
        $order = Order::with('items')->where('reference', $reference)->firstOrFail();

        return Inertia::render('Confirmation', [
            'order' => [
                'reference' => $order->reference,
                'customer_name' => $order->customer_name,
                'total' => $order->total,
                'currency' => 'MAD',
                'status' => $order->status->label(),
                'items' => $order->items,
            ],
            'whatsappUrl' => $this->whatsApp->orderConfirmed($order),
        ]);
    }

    public function track(Request $request): Response
    {
        $order = null;
        if ($ref = $request->string('ref')->toString()) {
            $order = Order::with('items')
                ->where('reference', $ref)
                ->when($request->filled('phone'), function ($q) use ($request) {
                    $phone = preg_replace('/\D/', '', $request->string('phone'));
                    $q->whereRaw('REPLACE(REPLACE(REPLACE(customer_phone, " ", ""), "+", ""), "-", "") LIKE ?', ['%'.$phone]);
                })
                ->first();
        }

        return Inertia::render('Suivi', [
            'prefillRef' => $ref ?: null,
            'prefillPhone' => $request->string('phone')->toString() ?: null,
            'order' => $order ? [
                'reference' => $order->reference,
                'status' => $order->status->value,
                'status_label' => $order->status->label(),
                'total' => $order->total,
                'currency' => 'MAD',
                'customer_name' => $order->customer_name,
                'estimated_delivery_at' => $order->estimated_delivery_at?->format('d/m/Y'),
                'items' => $order->items,
                'can_review' => $order->status->value === 'delivered',
            ] : null,
            'whatsappTrack' => $order ? $this->whatsApp->trackOrder($order->reference) : null,
        ]);
    }

    public function reviewsPage(Request $request): Response
    {
        $productId = null;
        if ($slug = $request->string('product')->toString()) {
            $productId = Product::query()->where('slug', $slug)->value('id');
        }

        return Inertia::render('Avis', [
            'prefillRef' => $request->string('ref')->toString() ?: null,
            'prefillPhone' => $request->string('phone')->toString() ?: null,
            'prefillProductId' => $productId,
            'whatsappSatisfaction' => $this->whatsApp->general(),
        ]);
    }

    /**
     * Display all orders in admin panel
     */
    public function index(): Response
    {
        $orders = Order::with('items', 'payments')
            ->orderByDesc('created_at')
            ->paginate(50);

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Display order details in admin panel
     */
    public function show(string $id): Response
    {
        $order = Order::with('items', 'payments')->findOrFail($id);

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Handle webhook from showcase site for new validated orders
     */
    public function webhook(Request $request): JsonResponse
    {
        // Validate webhook signature (if needed)
        $validated = $request->validate([
            'items' => 'required|array',
            'customer' => 'required|array',
        ]);

        // Create order from webhook data
        $order = $this->orders->createFromCart(
            $validated['items'],
            $validated['customer'],
        );

        // Send confirmation
        $this->whatsApp->orderConfirmed($order);

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'reference' => $order->reference,
        ], 201);
    }
}

