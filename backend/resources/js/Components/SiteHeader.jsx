import { Link } from '@inertiajs/react';
import { useCart } from '../hooks/useCart';

export default function SiteHeader() {
    const { itemCount } = useCart();

    const linkClass = 'text-xs font-medium uppercase tracking-widest text-stone-600 hover:text-stone-900';

    return (
        <header className="sticky top-0 z-50">
            <div className="bg-stone-900 px-4 py-2 text-center text-[11px] uppercase tracking-[0.2em] text-stone-300">
                Livraison Gabon ~8 jours · Stock Maroc · Paiement à la livraison (COD)
            </div>

            <div className="border-b border-stone-200/80 bg-stone-50/95 backdrop-blur-md">
                <div className="mx-auto flex h-16 max-w-6xl items-center justify-between px-4">
                    <Link href="/collection" className="group flex flex-col">
                        <span className="font-serif text-2xl tracking-[0.15em] text-stone-900 group-hover:opacity-80">
                            C7Pourt3
                        </span>
                        <span className="text-[10px] uppercase tracking-[0.35em] text-stone-500">
                            Sacs de luxe
                        </span>
                    </Link>

                    <nav className="flex items-center gap-6 sm:gap-8">
                        <Link href="/collection?sort=bestseller" className={linkClass}>
                            Plus vendu
                        </Link>
                        <Link href="/collection?sort=newest" className={linkClass}>
                            Nouveautés
                        </Link>
                        <Link href="/panier" className={linkClass}>
                            Panier
                            {itemCount > 0 && (
                                <span className="ml-1.5 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-stone-900 px-1.5 text-[10px] text-white">
                                    {itemCount}
                                </span>
                            )}
                        </Link>
                    </nav>
                </div>
            </div>
        </header>
    );
}
