import { Link, usePage } from '@inertiajs/react';
import { useCallback, useEffect, useState } from 'react';
import ShopLayout from '../Layouts/ShopLayout';
import { apiJson } from '../lib/api';

export default function Avis({ prefillRef, prefillPhone, prefillProductId }) {
    const { app } = usePage().props;
    const [ref, setRef] = useState(prefillRef ?? '');
    const [phone, setPhone] = useState(prefillPhone ?? '');
    const [order, setOrder] = useState(null);
    const [productId, setProductId] = useState(prefillProductId ? String(prefillProductId) : '');
    const [authorName, setAuthorName] = useState('');
    const [rating, setRating] = useState(5);
    const [comment, setComment] = useState('');
    const [msg, setMsg] = useState('');
    const [err, setErr] = useState('');
    const [checking, setChecking] = useState(false);

    const lookup = useCallback(async (reference, customerPhone) => {
        const r = (reference ?? ref).trim();
        const p = (customerPhone ?? phone).trim();
        if (!r || !p) {
            setErr('Indiquez la référence commande et votre téléphone.');
            return;
        }

        setChecking(true);
        setErr('');
        setOrder(null);

        const { res, data } = await apiJson(
            `/api/orders/reviewable?reference=${encodeURIComponent(r)}&phone=${encodeURIComponent(p)}`,
        );

        setChecking(false);

        if (!res.ok || !data.order) {
            setErr('Commande non trouvée ou pas encore livrée pour ce numéro.');
            return;
        }

        setOrder(data.order);
        setAuthorName(data.order.customer_name);

        const preferred = prefillProductId
            ? data.order.products.find((x) => x.product_id === prefillProductId && !x.already_reviewed)
            : null;
        const first = preferred ?? data.order.products.find((x) => !x.already_reviewed);

        if (first) {
            setProductId(String(first.product_id));
        } else {
            setErr('Tous les produits de cette commande ont déjà un avis.');
        }
    }, [ref, phone, prefillProductId]);

    useEffect(() => {
        if (prefillRef && prefillPhone) {
            lookup(prefillRef, prefillPhone);
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps -- une seule vérif. auto au chargement
    }, [prefillRef, prefillPhone]);

    const submit = async (e) => {
        e.preventDefault();
        setErr('');
        setMsg('');

        const { res, data } = await apiJson('/api/reviews', {
            method: 'POST',
            body: JSON.stringify({
                product_id: Number(productId),
                author_name: authorName,
                rating,
                comment,
                order_reference: ref.trim(),
                customer_phone: phone.trim(),
            }),
        });

        if (!res.ok) {
            setErr(data.message ?? Object.values(data.errors ?? {}).flat().join(' '));
            return;
        }

        setMsg(data.message);
        setComment('');
        setRating(5);
        await lookup();
    };

    const reviewableProducts = order?.products?.filter((p) => !p.already_reviewed) ?? [];

    return (
        <ShopLayout>
            <section className="mx-auto max-w-lg px-4 py-12">
                <h1 className="text-center font-serif text-4xl">Laisser un avis</h1>
                <p className="mt-2 text-center text-sm text-stone-500">
                    Après livraison de votre commande — badge <strong>Achat vérifié</strong>
                </p>

                <div className="mt-8 space-y-4">
                    <input
                        type="text"
                        placeholder="Référence commande (ex. C7-XXXXXXXX)"
                        value={ref}
                        onChange={(e) => setRef(e.target.value)}
                        className="w-full border border-stone-300 px-4 py-3 text-sm"
                    />
                    <input
                        type="tel"
                        placeholder="Téléphone utilisé à la commande"
                        value={phone}
                        onChange={(e) => setPhone(e.target.value)}
                        className="w-full border border-stone-300 px-4 py-3 text-sm"
                    />
                    <button
                        type="button"
                        onClick={() => lookup()}
                        disabled={checking}
                        className="w-full bg-stone-900 py-3 text-xs font-medium uppercase tracking-wider text-white disabled:opacity-60"
                    >
                        {checking ? 'Vérification…' : 'Vérifier ma commande'}
                    </button>
                </div>

                {err && <p className="mt-4 text-sm text-red-600">{err}</p>}
                {msg && <p className="mt-4 text-sm text-green-700">{msg}</p>}

                {order && reviewableProducts.length > 0 && (
                    <form onSubmit={submit} className="mt-8 space-y-4 border-t border-stone-200 pt-8">
                        <select
                            value={productId}
                            onChange={(e) => setProductId(e.target.value)}
                            className="w-full border border-stone-300 px-4 py-3 text-sm"
                            required
                        >
                            {order.products.map((p) => (
                                <option key={p.product_id} value={p.product_id} disabled={p.already_reviewed}>
                                    {p.name}
                                    {p.already_reviewed ? ' (avis déjà envoyé)' : ''}
                                </option>
                            ))}
                        </select>
                        <input
                            type="text"
                            value={authorName}
                            onChange={(e) => setAuthorName(e.target.value)}
                            placeholder="Votre prénom"
                            className="w-full border border-stone-300 px-4 py-3 text-sm"
                            required
                        />
                        <div>
                            <p className="text-xs font-medium uppercase tracking-widest text-stone-600">Note</p>
                            <div className="mt-2 flex gap-2">
                                {[1, 2, 3, 4, 5].map((n) => (
                                    <button
                                        key={n}
                                        type="button"
                                        onClick={() => setRating(n)}
                                        className={`h-10 w-10 border ${rating >= n ? 'border-stone-900 bg-stone-900 text-white' : 'border-stone-300'}`}
                                        aria-label={`${n} étoile${n > 1 ? 's' : ''}`}
                                    >
                                        {n}
                                    </button>
                                ))}
                            </div>
                        </div>
                        <textarea
                            value={comment}
                            onChange={(e) => setComment(e.target.value)}
                            placeholder="Votre commentaire (optionnel)"
                            rows={4}
                            className="w-full border border-stone-300 px-4 py-3 text-sm"
                        />
                        <button
                            type="submit"
                            className="w-full bg-stone-900 py-4 text-xs font-medium uppercase tracking-wider text-white hover:bg-stone-800"
                        >
                            Envoyer mon avis
                        </button>
                        <p className="text-center text-[11px] text-stone-400">
                            Votre avis sera publié après validation par C7Pourt3.
                        </p>
                    </form>
                )}

                <p className="mt-8 text-center text-sm text-stone-500">
                    <Link href="/suivi" className="underline hover:text-stone-900">
                        Suivre ma commande
                    </Link>
                    {' · '}
                    <a
                        href={`https://wa.me/${app?.whatsapp}?text=${encodeURIComponent('Bonjour C7Pourt3, question sur mon avis.')}`}
                        className="underline hover:text-stone-900"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Aide WhatsApp
                    </a>
                </p>
            </section>
        </ShopLayout>
    );
}
