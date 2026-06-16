import { router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import ShopLayout from '../Layouts/ShopLayout';
import { useCart } from '../hooks/useCart';

function formatFcfa(n) {
    return new Intl.NumberFormat('fr-FR').format(n) + ' FCFA';
}

export default function Checkout({ shippingFee, whatsappCheckout }) {
    const { items, total, clearCart } = useCart();
    const [loading, setLoading] = useState(false);
    const [err, setErr] = useState('');
    const [form, setForm] = useState({
        name: '',
        email: '',
        phone: '',
        city: 'Libreville',
        address: '',
        notes: '',
    });

    const grandTotal = total + (shippingFee ?? 0);

    const submit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setErr('');
        try {
            const res = await fetch('/api/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify({
                    customer: form,
                    items: items.map((i) => ({ variant_id: i.variantId, quantity: i.quantity })),
                }),
            });
            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.message ?? Object.values(data.errors ?? {}).flat().join(' '));
            }
            clearCart();
            router.visit(data.redirect);
        } catch (ex) {
            setErr(ex.message ?? 'Erreur lors de la commande.');
        } finally {
            setLoading(false);
        }
    };

    if (items.length === 0) {
        return (
            <ShopLayout>
                <p className="py-20 text-center">Panier vide.</p>
            </ShopLayout>
        );
    }

    return (
        <ShopLayout>
            <section className="mx-auto max-w-lg px-4 py-12">
                <h1 className="text-center font-serif text-4xl">Commander</h1>
                <p className="mt-2 text-center text-sm text-stone-500">Paiement à la livraison (COD) — Gabon</p>

                <form onSubmit={submit} className="mt-10 space-y-4">
                    {['name', 'email', 'phone', 'city', 'address'].map((field) => (
                        <input
                            key={field}
                            type={field === 'email' ? 'email' : field === 'phone' ? 'tel' : 'text'}
                            required={field !== 'email'}
                            placeholder={
                                {
                                    name: 'Nom complet',
                                    email: 'Email (optionnel)',
                                    phone: 'Téléphone WhatsApp',
                                    city: 'Ville (Gabon)',
                                    address: 'Adresse de livraison',
                                }[field]
                            }
                            value={form[field]}
                            onChange={(e) => setForm({ ...form, [field]: e.target.value })}
                            className="w-full border border-stone-300 px-4 py-3 text-sm"
                        />
                    ))}
                    <textarea
                        placeholder="Notes (optionnel)"
                        value={form.notes}
                        onChange={(e) => setForm({ ...form, notes: e.target.value })}
                        className="w-full border border-stone-300 px-4 py-3 text-sm"
                        rows={2}
                    />
                    <div className="border-t border-stone-200 pt-4 text-sm">
                        <p>Sous-total : {formatFcfa(total)}</p>
                        <p>Livraison : {formatFcfa(shippingFee)}</p>
                        <p className="mt-2 text-lg font-medium">Total : {formatFcfa(grandTotal)}</p>
                    </div>
                    {err && <p className="text-sm text-red-600">{err}</p>}
                    <button
                        type="submit"
                        disabled={loading}
                        className="w-full bg-stone-900 py-4 text-xs font-medium uppercase tracking-wider text-white disabled:opacity-50"
                    >
                        {loading ? 'Envoi…' : 'Confirmer la commande'}
                    </button>
                </form>

                <a
                    href={whatsappCheckout}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="mt-6 block text-center text-sm text-stone-600 underline"
                >
                    Une question ? Contactez-nous sur WhatsApp
                </a>
            </section>
        </ShopLayout>
    );
}
