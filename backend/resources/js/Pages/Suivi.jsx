import { Link, router } from '@inertiajs/react';
import { useState } from 'react';
import ShopLayout from '../Layouts/ShopLayout';

function formatFcfa(n) {
    return new Intl.NumberFormat('fr-FR').format(n) + ' FCFA';
}

const steps = [
    { key: 'pending', label: 'En attente' },
    { key: 'confirmed', label: 'Confirmée' },
    { key: 'shipped_morocco', label: 'Expédiée (Maroc)' },
    { key: 'arrived_gabon', label: 'Arrivée au Gabon' },
    { key: 'delivered', label: 'Livrée' },
];

export default function Suivi({ order: initial, whatsappTrack, prefillRef, prefillPhone }) {
    const [ref, setRef] = useState(prefillRef ?? '');
    const [phone, setPhone] = useState(prefillPhone ?? '');

    const search = () => {
        router.get('/suivi', { ref, phone }, { preserveState: true });
    };

    const order = initial;
    const stepIndex = order ? steps.findIndex((s) => s.key === order.status) : -1;

    return (
        <ShopLayout>
            <section className="mx-auto max-w-lg px-4 py-12">
                <h1 className="text-center font-serif text-4xl">Suivi commande</h1>
                <div className="mt-8 flex gap-2">
                    <input
                        type="text"
                        placeholder="Référence C7-…"
                        value={ref}
                        onChange={(e) => setRef(e.target.value)}
                        className="flex-1 border border-stone-300 px-4 py-3 text-sm"
                    />
                    <button type="button" onClick={search} className="bg-stone-900 px-6 text-white text-xs uppercase">
                        OK
                    </button>
                </div>
                <input
                    type="tel"
                    placeholder="Téléphone (optionnel)"
                    value={phone}
                    onChange={(e) => setPhone(e.target.value)}
                    className="mt-2 w-full border border-stone-300 px-4 py-3 text-sm"
                />

                {order && (
                    <div className="mt-10">
                        <p className="text-center text-lg font-medium">{order.reference}</p>
                        <p className="text-center text-stone-600">{order.status_label}</p>
                        <ul className="mt-8 space-y-2">
                            {steps.map((s, i) => (
                                <li
                                    key={s.key}
                                    className={`text-sm ${i <= stepIndex ? 'font-medium text-stone-900' : 'text-stone-400'}`}
                                >
                                    {i <= stepIndex ? '✓' : '○'} {s.label}
                                </li>
                            ))}
                        </ul>
                        <p className="mt-6 text-center text-sm">Total : {formatFcfa(order.total)}</p>
                        {order.can_review && (
                            <Link
                                href={`/avis?ref=${order.reference}${phone ? `&phone=${encodeURIComponent(phone)}` : ''}`}
                                className="mt-6 block text-center text-sm font-medium underline"
                            >
                                Laisser un avis — Achat vérifié
                            </Link>
                        )}
                        {whatsappTrack && (
                            <a href={whatsappTrack} target="_blank" rel="noopener noreferrer" className="mt-4 block text-center text-sm text-stone-600 underline">
                                WhatsApp
                            </a>
                        )}
                    </div>
                )}
            </section>
        </ShopLayout>
    );
}
