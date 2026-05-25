import { Link, usePage } from '@inertiajs/react';

const perks = [
    { title: 'LIVRAISON GABON', desc: 'Expédition depuis le Maroc — livraison estimée sous 8 jours.' },
    { title: 'PAIEMENT COD', desc: 'Payez à la réception de votre colis au Gabon.' },
    { title: 'SUPPORT WHATSAPP', desc: 'Une question ? Contactez-nous sur WhatsApp.' },
    { title: 'C7POURT3', desc: 'Sacs de luxe sélectionnés pour vous.' },
];

export default function ShopFooter() {
    const { app } = usePage().props;
    const fb = app?.facebook ?? 'https://web.facebook.com/profile.php?id=61590177099769';
    const wa = `https://wa.me/${app?.whatsapp ?? '24100000000'}`;
    const dev = app?.developer ?? { name: 'Clainn', url: 'https://www.linkedin.com/in/clainn/' };

    return (
        <footer className="mt-20">
            {/* Newsletter */}
            <section className="border-y border-stone-200 bg-white px-4 py-12 text-center">
                <p className="text-xs uppercase tracking-[0.25em] text-stone-500">Newsletter</p>
                <p className="mx-auto mt-3 max-w-lg text-sm text-stone-600">
                    Inscrivez-vous pour recevoir nos nouveautés et offres exclusives C7Pourt3.
                </p>
                <form className="mx-auto mt-6 flex max-w-md gap-0" onSubmit={(e) => e.preventDefault()}>
                    <input
                        type="email"
                        placeholder="votre-courriel@exemple.com"
                        className="flex-1 border border-stone-300 px-4 py-3 text-sm outline-none"
                    />
                    <button type="submit" className="bg-stone-900 px-6 py-3 text-xs font-medium uppercase tracking-wider text-white">
                        S&apos;inscrire
                    </button>
                </form>
            </section>

            {/* Avantages */}
            <section className="grid grid-cols-2 gap-8 border-b border-stone-200 bg-white px-6 py-10 md:grid-cols-4 md:px-12">
                {perks.map((p) => (
                    <div key={p.title} className="text-center">
                        <p className="text-xs font-medium uppercase tracking-widest text-stone-900">{p.title}</p>
                        <p className="mt-2 text-xs leading-relaxed text-stone-500">{p.desc}</p>
                    </div>
                ))}
            </section>

            {/* Colonnes + promo */}
            <section className="bg-stone-900 text-stone-300">
                <div className="mx-auto grid max-w-6xl gap-10 px-6 py-14 md:grid-cols-4">
                    <div>
                        <p className="text-xs font-medium uppercase tracking-widest text-white">Découvrir</p>
                        <ul className="mt-4 space-y-2 text-sm">
                            <li><Link href="/collection" className="hover:text-white">Collection</Link></li>
                            <li><Link href="/collection?sort=newest" className="hover:text-white">Nouveautés</Link></li>
                            <li><Link href="/collection?category=luxe" className="hover:text-white">Luxe</Link></li>
                            <li><Link href="/collection?category=soiree" className="hover:text-white">Soirée</Link></li>
                        </ul>
                    </div>
                    <div>
                        <p className="text-xs font-medium uppercase tracking-widest text-white">Aide & support</p>
                        <ul className="mt-4 space-y-2 text-sm">
                            <li><a href={wa} target="_blank" rel="noopener noreferrer" className="hover:text-white">WhatsApp</a></li>
                            <li><a href={fb} target="_blank" rel="noopener noreferrer" className="hover:text-white">Facebook C7Pourt3</a></li>
                            <li><span className="text-stone-500">Paiement à la livraison</span></li>
                        </ul>
                    </div>
                    <div>
                        <p className="text-xs font-medium uppercase tracking-widest text-white">Infos</p>
                        <ul className="mt-4 space-y-2 text-sm">
                            <li>Stock : Maroc</li>
                            <li>Livraison : Gabon</li>
                            <li>Délai : ~8 jours</li>
                        </ul>
                    </div>
                    <div className="rounded bg-stone-700 p-6 text-white md:col-span-1">
                        <p className="text-lg font-medium uppercase leading-tight tracking-wide">
                            Offres exclusives pour vous !
                        </p>
                        <p className="mt-3 text-xs text-stone-300">
                            Suivez C7Pourt3 sur Facebook pour les nouveautés et codes promo.
                        </p>
                        <a
                            href={fb}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="mt-4 inline-block border-b border-white text-sm uppercase tracking-wider hover:opacity-80"
                        >
                            Suivre sur Facebook →
                        </a>
                    </div>
                </div>

                {/* Barre bas — sans mention Laravel */}
                <div className="border-t border-stone-800 px-6 py-5 text-center text-[10px] uppercase tracking-widest text-stone-500">
                    <p className="text-stone-400">
                        © {new Date().getFullYear()} C7Pourt3 — Tous droits réservés
                    </p>
                    <p className="mt-3 normal-case tracking-normal text-stone-500">
                        Sacs de luxe · Livraison Gabon · Paiement à la livraison
                    </p>
                    <p className="mt-2 normal-case tracking-normal">
                        Développé par{' '}
                        <a
                            href={dev.url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="text-stone-400 underline hover:text-white"
                        >
                            {dev.name}
                        </a>
                    </p>
                </div>
            </section>
        </footer>
    );
}
