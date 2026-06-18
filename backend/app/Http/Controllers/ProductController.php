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
        if ($sort === 'bestseller') {
            $q->where('is_featured', true);
        }
        $q = match ($sort) {
            'bestseller', 'featured' => $q->orderByDesc('created_at'),
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

    public function index(Request $request)
    {
        $q = Product::with(['variants' => fn ($v) => $v->where('is_active', true)])->where('is_active', true);
        if ($cat = $request->string('category')->toString()) {
            $q->where('category', $cat);
        }
        $sort = $request->string('sort', 'featured')->toString();
        if ($sort === 'bestseller') {
            $q->where('is_featured', true);
        }
        $q = match ($sort) {
            'bestseller', 'featured' => $q->orderByDesc('created_at'),
            'price_asc' => $q->orderBy('base_price'),
            'price_desc' => $q->orderByDesc('base_price'),
            'newest' => $q->orderByDesc('created_at'),
            default => $q->orderByDesc('is_featured')->orderByDesc('created_at'),
        };

        return response()->json([
            'products' => $q->get()->map(fn ($p) => $this->fmt($p)),
        ]);
    }

    public function quickView(Product $product)
    {
        $product->load(['variants' => fn ($v) => $v->where('is_active', true)]);

        return response()->json(['product' => $this->fmt($product, true)]);
    }

    /**
     * Display product management page for admin (CRUD interface)
     */
    public function manage(Request $request)
    {
        $products = Product::orderByDesc('created_at')->get();

        return Inertia::render('Products/Manage', [
            'products' => $products,
        ]);
    }

    /**
     * Show create product form
     */
    public function create()
    {
        return Inertia::render('Products/Create');
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products',
            'description' => 'nullable|string',
            'base_price' => 'required|integer|min:0',
            'compare_at_price' => 'nullable|integer|min:0',
            'category' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        Product::create($validated);

        return redirect()->route('products.manage')->with('success', 'Produit créé avec succès');
    }

    /**
     * Show edit product form
     */
    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', [
            'product' => $product,
        ]);
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'base_price' => 'required|integer|min:0',
            'compare_at_price' => 'nullable|integer|min:0',
            'category' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $product->update($validated);

        return redirect()->route('products.manage')->with('success', 'Produit mis à jour avec succès');
    }

    /**
     * Delete the specified product
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.manage')->with('success', 'Produit supprimé avec succès');
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
