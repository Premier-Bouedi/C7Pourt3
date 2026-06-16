import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { apiJson } from '../lib/api';
import SocialLinks from './SocialLinks';

const footerLink = 'hover:text-white transition-colors';

const discoverLinks = [
    { label: 'Collection', href: '/collection' },
    { label: 'Plus vendu', href: '/collection?sort=bestseller' },
    { label: 'Nouveautés', href: '/collection?sort=newest' },
    { label: 'Luxe', href: '/collection?category=luxe' },
    { label: 'Soirée', href: '/collection?category=soiree' },
    { label: 'Quotidien', href: '/collection?category=quotidien' },
    { label: 'Bandoulière', href: '/collection?category=bandouliere' },
];

const helpLinks = [
    { label: 'Suivi commande', href: '/suivi' },
    { label: 'Laisser un avis', href: '/avis' },
    { label: 'Paiement à la livraison', href: '/infos#cod' },
    { label: 'Passer commande', href: '/commander' },
];

const infoLinks = [
    { label: 'Stock : Maroc', href: '/infos#stock' },
    { label: 'Livraison : Gabon', href: '/infos#livraison' },
    { label: 'Délai : ~8 jours', href: '/infos#livraison' },
];

export default function ShopFooter() {
    const { app } = usePage().props;
    const dev = app?.developer ?? { name: 'Clainn', url: 'https://www.linkedin.com/in/clainn/' };
    const waUrl = app?.whatsappUrls?.general ?? `https://wa.me/${app?.whatsapp ?? '24100000000'}`;
    const [email, setEmail] = useState('');
    const [newsletterMsg, setNewsletterMsg] = useState('');
    const [newsletterErr, setNewsletterErr] = useState('');
    const [newsletterLoading, setNewsletterLoading] = useState(false);

    const subscribeNewsletter = async (e) => {
        e.preventDefault();
        setNewsletterMsg('');
        setNewsletterErr('');
        setNewsletterLoading(true);

        const { res, data } = await apiJson('/api/newsletter', {
            method: 'POST',
            body: JSON.stringify({ email: email.trim() }),
        });

        setNewsletterLoading(false);

        if (!res.ok) {
            const errText =
                data.message ??
                (data.errors?.email ? data.errors.email.join(' ') : 'Inscription impossible. Réessayez.');
            setNewsletterErr(errText);
            return;
        }

        setNewsletterMsg(data.message ?? 'Merci pour votre inscription !');
        setEmail('');
    };

    const perks = [
        { title: 'LIVRAISON GABON', desc: 'Expédition depuis le Maroc — livraison estimée sous 8 jours.', href: '/infos#livraison' },
        { title: 'PAIEMENT COD', desc: 'Payez à la réception de votre colis au Gabon.', href: '/infos#cod' },
        { title: 'SUPPORT WHATSAPP', desc: 'Une question ? Contactez-nous sur WhatsApp.', href: waUrl, external: true },
        { title: 'C7POURT3', desc: 'Sacs de luxe sélectionnés pour vous.', href: '/collection' },
    ];

    return (
        <footer className="mt-20">
            <section id="newsletter" className="border-y border-stone-200 bg-white px-4 py-12 text-center">
                <p className="text-xs uppercase tracking-[0.25em] text-stone-500">Newsletter</p>
                <p className="mx-auto mt-3 max-w-lg text-sm text-stone-600">
                    Inscrivez-vous pour recevoir nos nouveautés et offres exclusives C7Pourt3.
                </p>
                <form className="mx-auto mt-6 flex max-w-md gap-0" onSubmit={subscribeNewsletter}>
                    <input
                        type="email"
                        required
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        placeholder="votre-courriel@exemple.com"
                        disabled={newsletterLoading}
                        className="flex-1 border border-stone-300 px-4 py-3 text-sm outline-none focus:border-stone-900 disabled:opacity-60"
                    />
                    <button
                        type="submit"
                        disabled={newsletterLoading}
                        className="bg-stone-900 px-6 py-3 text-xs font-medium uppercase tracking-wider text-white hover:bg-stone-800 disabled:opacity-60"
                    >
                        {newsletterLoading ? '…' : "S'inscrire"}
                    </button>
                </form>
                {newsletterMsg && <p className="mx-auto mt-4 max-w-md text-sm text-green-700">{newsletterMsg}</p>}
                {newsletterErr && <p className="mx-auto mt-4 max-w-md text-sm text-red-600">{newsletterErr}</p>}
            </section>

            <section className="grid grid-cols-2 gap-8 border-b border-stone-200 bg-white px-6 py-10 md:grid-cols-4 md:px-12">
                {perks.map((p) => {
                    const inner = (
                        <>
                            <p className="text-xs font-medium uppercase tracking-widest text-stone-900 group-hover:underline">
                                {p.title}
                            </p>
                            <p className="mt-2 text-xs leading-relaxed text-stone-500">{p.desc}</p>
                        </>
                    );
                    return p.external ? (
                        <a
                            key={p.title}
                            href={p.href}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="group block text-center"
                        >
                            {inner}
                        </a>
                    ) : (
                        <Link key={p.title} href={p.href} className="group block text-center">
                            {inner}
                        </Link>
                    );
                })}
            </section>

            <section className="bg-stone-900 text-stone-300">
                <div className="mx-auto grid max-w-6xl gap-10 px-6 py-14 md:grid-cols-4">
                    <div>
                        <p className="text-xs font-medium uppercase tracking-widest text-white">Découvrir</p>
                        <ul className="mt-4 space-y-2 text-sm">
                            {discoverLinks.map((l) => (
                                <li key={l.href}>
                                    <Link href={l.href} className={footerLink}>
                                        {l.label}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </div>
                    <div>
                        <p className="text-xs font-medium uppercase tracking-widest text-white">Aide & support</p>
                        <ul className="mt-4 space-y-2 text-sm">
                            {helpLinks.map((l) => (
                                <li key={l.href}>
                                    <Link href={l.href} className={footerLink}>
                                        {l.label}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </div>
                    <div>
                        <p className="text-xs font-medium uppercase tracking-widest text-white">Infos</p>
                        <ul className="mt-4 space-y-2 text-sm">
                            {infoLinks.map((l) => (
                                <li key={l.label}>
                                    <Link href={l.href} className={footerLink}>
                                        {l.label}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </div>
                    <div className="flex flex-col items-center justify-center rounded bg-stone-700 p-6 text-white md:col-span-1">
                        <p className="text-center text-lg font-medium uppercase leading-tight tracking-wide">
                            Suivez C7Pourt3
                        </p>
                        <p className="mt-3 text-center text-xs text-stone-300">
                            Nouveautés, offres et contact direct
                        </p>
                        <SocialLinks size="lg" className="mt-6" />
                    </div>
                </div>

                <div className="border-t border-stone-800 px-6 py-8 text-center">
                    <p className="text-xs font-medium uppercase tracking-[0.25em] text-stone-400">
                        Nos réseaux
                    </p>
                    <SocialLinks className="mt-5" />
                </div>

                <div className="border-t border-stone-800 px-6 py-5 text-center text-[10px] uppercase tracking-widest text-stone-500">
                    <p className="text-stone-400">
                        © {new Date().getFullYear()} C7Pourt3 — Tous droits réservés
                    </p>
                    <p className="mt-3 normal-case tracking-normal text-stone-500">
                        <Link href="/collection" className={`${footerLink} normal-case`}>
                            Sacs de luxe
                        </Link>
                        {' · '}
                        <Link href="/infos#livraison" className={`${footerLink} normal-case`}>
                            Livraison Gabon
                        </Link>
                        {' · '}
                        <Link href="/infos#cod" className={`${footerLink} normal-case`}>
                            Paiement à la livraison
                        </Link>
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
