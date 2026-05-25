<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function collection(Request $request)
    {
        $q = Product::with(['variants' => fn ($v) => $v->where('is_active', true)])->where('is_active', true);
        if ($cat = $request->string('category')->toString()) {
            $q->where('category', $cat);
        }
        $sort = $request->string('sort', 'featured')->toString();
        $q = match ($sort) {
            'price_asc' => $q->orderBy('base_price'),
            'price_desc' => $q->orderByDesc('base_price'),
            'newest' => $q->orderByDesc('created_at'),
            default => $q->orderByDesc('is_featured')->orderByDesc('created_at'),
        };

        return Inertia::render('Collection', [
            'products' => $q->paginate(12)->through(fn ($p) => $this->fmt($p)),
            'filters' => ['category' => $cat ?? null, 'sort' => $sort],
            'categories' => Product::where('is_active', true)->whereNotNull('category')->distinct()->pluck('category'),
        ]);
    }

    public function quickView(Product $product)
    {
        $product->load(['variants' => fn ($v) => $v->where('is_active', true)]);

        return response()->json(['product' => $this->fmt($product, true)]);
    }

    private function fmt(Product $p, bool $d = false): array
    {
        $data = [
            'id' => $p->id, 'name' => $p->name, 'slug' => $p->slug,
            'base_price' => $p->displayPrice(),
            'compare_at_price' => (int) ($p->compare_at_price ?? 0),
            'images' => $p->images ?? [],
            'category' => $p->category, 'average_rating' => (float) $p->average_rating,
            'reviews_count' => $p->reviews_count,
            'variants' => $p->variants->map(fn ($v) => [
                'id' => $v->id,
                'sku' => $v->sku,
                'color' => $v->color,
                'color_hex' => $v->color_hex,
                'price' => $v->effectivePrice(),
                'image' => $v->image ?? ($p->images[0] ?? null),
                'in_stock' => $v->isInStock(),
            ]),
        ];
        if ($d) {
            $data['description'] = $p->description;
        }

        return $data;
    }
}
