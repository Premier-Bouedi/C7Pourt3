import { Link } from '@inertiajs/react';
import { useEffect } from 'react';
import ShopLayout from '../Layouts/ShopLayout';

const sections = [
    {
        id: 'stock',
        title: 'Stock au Maroc',
        body: 'Nos sacs sont préparés et expédiés depuis notre stock au Maroc. Chaque commande est vérifiée avant envoi vers le Gabon.',
    },
    {
        id: 'livraison',
        title: 'Livraison au Gabon',
        body: 'Livraison estimée sous environ 8 jours ouvrés après confirmation de votre commande. Vous recevez un message WhatsApp avec les détails de suivi.',
    },
    {
        id: 'cod',
        title: 'Paiement à la livraison (COD)',
        body: 'Vous réglez le montant total (produits + frais de livraison) directement au livreur, en espèces, à la réception de votre colis. Aucun paiement en ligne n’est requis.',
    },
];

export default function Infos() {
    useEffect(() => {
        const hash = window.location.hash;
        if (!hash) {
            return;
        }
        document.querySelector(hash)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, []);

    return (
        <ShopLayout>
            <section className="mx-auto max-w-2xl px-4 py-12 md:py-16">
                <header className="text-center">
                    <p className="text-xs uppercase tracking-[0.3em] text-stone-500">C7Pourt3</p>
                    <h1 className="mt-2 font-serif text-3xl md:text-4xl">Informations</h1>
                    <p className="mt-3 text-sm text-stone-600">
                        Livraison, délais et paiement à la livraison
                    </p>
                </header>

                <div className="mt-12 space-y-10">
                    {sections.map((s) => (
                        <article key={s.id} id={s.id} className="scroll-mt-28 border-b border-stone-200 pb-10 last:border-0">
                            <h2 className="font-serif text-xl text-stone-900">{s.title}</h2>
                            <p className="mt-3 text-sm leading-relaxed text-stone-600">{s.body}</p>
                        </article>
                    ))}
                </div>

                <div className="mt-12 flex flex-col items-center gap-4 text-center text-sm">
                    <Link
                        href="/commander"
                        className="rounded-full bg-stone-900 px-8 py-3 text-xs font-medium uppercase tracking-wider text-white hover:bg-stone-800"
                    >
                        Passer commande
                    </Link>
                    <Link href="/suivi" className="text-stone-600 underline hover:text-stone-900">
                        Suivre une commande
                    </Link>
                </div>
            </section>
        </ShopLayout>
    );
}
