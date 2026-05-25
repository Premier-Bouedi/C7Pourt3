import { router } from '@inertiajs/react';
import { useCallback, useState } from 'react';
import ProductCard from '../Components/ProductCard';
import QuickViewModal from '../Components/QuickViewModal';
import ShopLayout from '../Layouts/ShopLayout';
import { useCart } from '../hooks/useCart';

const sortOptions = [
    { value: 'featured', label: 'En vedette' },
    { value: 'newest', label: 'Nouveautés' },
    { value: 'price_asc', label: 'Prix croissant' },
    { value: 'price_desc', label: 'Prix décroissant' },
];

export default function Collection({ products, filters, categories }) {
    const [qp, setQp] = useState(null);
    const [open, setOpen] = useState(false);
    const { addItem } = useCart();

    const openQ = useCallback(async (p) => {
        const res = await fetch(`/api/products/${p.slug}/quick-view`);
        if (!res.ok) {
            return;
        }
        setQp((await res.json()).product);
        setOpen(true);
    }, []);

    const applySort = (sort) => {
        router.get('/collection', { ...filters, sort }, { preserveState: true });
    };

    return (
        <ShopLayout>
            <section className="mx-auto max-w-6xl px-4 py-10 md:py-14">
                <header className="text-center">
                    <p className="text-xs uppercase tracking-[0.3em] text-stone-500">C7Pourt3</p>
                    <h1 className="mt-2 font-serif text-4xl md:text-5xl">Collection</h1>
                    <p className="mx-auto mt-3 max-w-md text-sm text-stone-600">
                        Sacs de luxe — livraison Gabon ~8 jours — paiement à la livraison
                    </p>
                </header>

                <div className="mt-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div className="flex flex-wrap justify-center gap-2">
                        <button
                            type="button"
                            onClick={() => router.get('/collection', { sort: filters?.sort })}
                            className={`rounded-full px-4 py-1.5 text-sm capitalize transition ${
                                !filters?.category ? 'bg-stone-900 text-white' : 'bg-stone-100 hover:bg-stone-200'
                            }`}
                        >
                            Tous
                        </button>
                        {categories?.map((c) => (
                            <button
                                key={c}
                                type="button"
                                onClick={() => router.get('/collection', { category: c, sort: filters?.sort })}
                                className={`rounded-full px-4 py-1.5 text-sm capitalize transition ${
                                    filters?.category === c
                                        ? 'bg-stone-900 text-white'
                                        : 'bg-stone-100 hover:bg-stone-200'
                                }`}
                            >
                                {c}
                            </button>
                        ))}
                    </div>
                    <select
                        value={filters?.sort ?? 'featured'}
                        onChange={(e) => applySort(e.target.value)}
                        className="mx-auto rounded-full border border-stone-300 bg-white px-4 py-2 text-sm sm:mx-0"
                    >
                        {sortOptions.map((o) => (
                            <option key={o.value} value={o.value}>
                                {o.label}
                            </option>
                        ))}
                    </select>
                </div>

                <div className="mt-10 grid grid-cols-2 gap-4 sm:gap-6 md:grid-cols-3 lg:grid-cols-4">
                    {products.data.map((p) => (
                        <ProductCard key={p.id} product={p} onQuickView={openQ} />
                    ))}
                </div>

                {products.data.length === 0 && (
                    <p className="mt-16 text-center text-stone-500">Aucun produit dans cette catégorie.</p>
                )}
            </section>

            <QuickViewModal
                product={qp}
                open={open}
                onClose={() => setOpen(false)}
                onAddToCart={addItem}
            />
        </ShopLayout>
    );
}
