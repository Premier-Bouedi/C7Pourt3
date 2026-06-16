<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Product;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(private readonly ReviewService $reviews) {}

    public function forProduct(Product $product): JsonResponse
    {
        $list = $product->approvedReviews()
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn (Review $r) => [
                'id' => $r->id,
                'author_name' => $r->author_name,
                'rating' => $r->rating,
                'comment' => $r->comment,
                'is_verified_purchase' => $r->is_verified_purchase,
                'created_at' => $r->created_at->toIso8601String(),
            ]);

        return response()->json(['reviews' => $list]);
    }

    public function store(StoreReviewRequest $request): JsonResponse
    {
        $review = $this->reviews->create($request->validated());

        return response()->json([
            'message' => 'Merci ! Votre avis sera publié après validation par C7Pourt3.',
            'review' => [
                'id' => $review->id,
                'is_verified_purchase' => $review->is_verified_purchase,
            ],
        ], 201);
    }

    public function reviewableOrder(Request $request): JsonResponse
    {
        $request->validate([
            'reference' => ['required', 'string'],
            'phone' => ['required', 'string'],
        ]);

        $order = $this->reviews->findReviewableOrder(
            $request->string('reference'),
            $request->string('phone'),
        );

        if (! $order) {
            return response()->json(['order' => null], 404);
        }

        return response()->json([
            'order' => [
                'reference' => $order->reference,
                'customer_name' => $order->customer_name,
                'products' => $order->items->map(fn ($i) => [
                    'product_id' => $i->product_id,
                    'name' => $i->product_name,
                    'already_reviewed' => Review::where('order_id', $order->id)
                        ->where('product_id', $i->product_id)
                        ->exists(),
                ]),
            ],
        ]);
    }
}
