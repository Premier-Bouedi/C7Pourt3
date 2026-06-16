<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReviewService
{
    public function findReviewableOrder(string $reference, string $phone): ?Order
    {
        $phone = preg_replace('/\D/', '', $phone);

        return Order::with(['items.product'])
            ->where('reference', $reference)
            ->where('status', OrderStatus::Delivered)
            ->whereRaw('REPLACE(REPLACE(REPLACE(customer_phone, " ", ""), "+", ""), "-", "") LIKE ?', ['%'.$phone])
            ->first();
    }

    /**
     * @throws ValidationException
     */
    public function create(array $data): Review
    {
        $product = Product::findOrFail($data['product_id']);
        $order = null;
        $verified = false;

        if (! empty($data['order_reference']) && ! empty($data['customer_phone'])) {
            $order = $this->findReviewableOrder($data['order_reference'], $data['customer_phone']);

            if (! $order) {
                throw ValidationException::withMessages([
                    'order_reference' => 'Commande introuvable ou non livrée pour ce numéro.',
                ]);
            }

            $hasProduct = $order->items->contains('product_id', $product->id);

            if (! $hasProduct) {
                throw ValidationException::withMessages([
                    'product_id' => 'Ce produit ne fait pas partie de la commande livrée.',
                ]);
            }

            $already = Review::where('order_id', $order->id)
                ->where('product_id', $product->id)
                ->exists();

            if ($already) {
                throw ValidationException::withMessages([
                    'product_id' => 'Un avis existe déjà pour ce produit sur cette commande.',
                ]);
            }

            $verified = true;
        }

        return DB::transaction(function () use ($data, $product, $order, $verified) {
            $review = Review::create([
                'product_id' => $product->id,
                'order_id' => $order?->id,
                'author_name' => $data['author_name'],
                'rating' => (int) $data['rating'],
                'comment' => $data['comment'] ?? null,
                'is_approved' => false,
                'is_verified_purchase' => $verified,
            ]);

            return $review;
        });
    }

    public function syncProductRatings(Product $product): void
    {
        $approved = $product->approvedReviews();
        $product->update([
            'average_rating' => round((float) ($approved->avg('rating') ?? 0), 2),
            'reviews_count' => $approved->count(),
        ]);
    }

    public function approve(Review $review): void
    {
        $review->update(['is_approved' => true]);
        $this->syncProductRatings($review->product);
    }
}
