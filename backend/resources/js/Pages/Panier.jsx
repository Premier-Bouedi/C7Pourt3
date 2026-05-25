import { Link } from '@inertiajs/react';
import ShopLayout from '../Layouts/ShopLayout';
import { useCart } from '../hooks/useCart';

function formatFcfa(n) {
    return new Intl.NumberFormat('fr-FR').format(n) + ' FCFA';
}

export default function Panier() {
    const { items, removeItem, setQuantity, total, itemCount } = useCart();

    return (
        <ShopLayout>
            <section className="mx-auto max-w-3xl px-4 py-12">
                <h1 className="text-center font-serif text-4xl">Panier</h1>
                <p className="mt-2 text-center text-sm text-stone-500">
                    {itemCount === 0 ? 'Votre panier est vide' : `${itemCount} article${itemCount > 1 ? 's' : ''}`}
                </p>

                {items.length === 0 ? (
                    <div className="mt-12 text-center">
                        <Link
                            href="/collection"
                            className="inline-block rounded-full bg-stone-900 px-8 py-3 text-xs font-medium uppercase tracking-wider text-white"
                        >
                            Voir la collection
                        </Link>
                    </div>
                ) : (
                    <ul className="mt-10 divide-y divide-stone-200">
                        {items.map((item) => (
                            <li key={item.variantId} className="flex gap-4 py-6">
                                {item.image && (
                                    <img
                                        src={item.image}
                                        alt={item.name}
                                        className="h-24 w-20 shrink-0 object-cover"
                                    />
                                )}
                                <div className="min-w-0 flex-1">
                                    <p className="font-medium text-stone-900">{item.name}</p>
                                    {item.color && (
                                        <p className="mt-0.5 text-sm text-stone-500">{item.color}</p>
                                    )}
                                    <p className="mt-1 text-sm">{formatFcfa(item.price)}</p>
                                    <div className="mt-3 flex items-center gap-3">
                                        <button
                                            type="button"
                                            onClick={() => setQuantity(item.variantId, item.quantity - 1)}
                                            className="h-8 w-8 border border-stone-300 text-stone-600"
                                        >
                                            −
                                        </button>
                                        <span className="w-6 text-center text-sm">{item.quantity}</span>
                                        <button
                                            type="button"
                                            onClick={() => setQuantity(item.variantId, item.quantity + 1)}
                                            className="h-8 w-8 border border-stone-300 text-stone-600"
                                        >
                                            +
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => removeItem(item.variantId)}
                                            className="ml-auto text-xs uppercase tracking-wider text-stone-500 underline"
                                        >
                                            Retirer
                                        </button>
                                    </div>
                                </div>
                                <p className="shrink-0 text-sm font-medium">
                                    {formatFcfa(item.price * item.quantity)}
                                </p>
                            </li>
                        ))}
                    </ul>
                )}

                {items.length > 0 && (
                    <div className="mt-8 border-t border-stone-200 pt-8">
                        <div className="flex justify-between text-lg font-medium">
                            <span>Total</span>
                            <span>{formatFcfa(total)}</span>
                        </div>
                        <p className="mt-2 text-center text-xs text-stone-500">
                            Paiement à la livraison (COD) — Gabon
                        </p>
                        <div className="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-center">
                            <Link
                                href="/collection"
                                className="rounded-full border border-stone-300 px-8 py-3 text-center text-xs font-medium uppercase tracking-wider"
                            >
                                Continuer mes achats
                            </Link>
                            <button
                                type="button"
                                disabled
                                className="rounded-full bg-stone-900 px-8 py-3 text-xs font-medium uppercase tracking-wider text-white opacity-50"
                                title="Page commander bientôt disponible"
                            >
                                Commander
                            </button>
                        </div>
                    </div>
                )}
            </section>
        </ShopLayout>
    );
}
