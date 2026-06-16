import { Link } from '@inertiajs/react';
import ShopLayout from '../Layouts/ShopLayout';

function formatFcfa(n) {
    return new Intl.NumberFormat('fr-FR').format(n) + ' FCFA';
}

export default function Confirmation({ order, whatsappUrl }) {
    return (
        <ShopLayout>
            <section className="mx-auto max-w-lg px-4 py-16 text-center">
                <p className="text-xs uppercase tracking-[0.3em] text-stone-500">Merci</p>
                <h1 className="mt-2 font-serif text-4xl">Commande confirmée</h1>
                <p className="mt-4 text-2xl font-medium">{order.reference}</p>
                <p className="mt-2 text-stone-600">
                    Bonjour {order.customer_name}, total {formatFcfa(order.total)} — {order.status}
                </p>
                <p className="mt-4 text-sm text-stone-500">
                    Un email de confirmation vous a été envoyé si vous avez indiqué une adresse email.
                </p>
                <p className="mt-4 text-sm text-stone-500">
                    Après réception de votre colis, vous pourrez laisser un avis avec le badge{' '}
                    <strong>Achat vérifié</strong>.
                </p>
                <div className="mt-10 flex flex-col gap-3">
                    <Link
                        href={`/suivi?ref=${order.reference}`}
                        className="rounded-full bg-stone-900 px-8 py-3 text-xs font-medium uppercase tracking-wider text-white"
                    >
                        Suivre ma commande
                    </Link>
                    <Link
                        href={`/avis?ref=${order.reference}`}
                        className="rounded-full border border-stone-300 px-8 py-3 text-xs font-medium uppercase tracking-wider text-stone-800 hover:bg-stone-50"
                    >
                        Laisser un avis (après livraison)
                    </Link>
                    <a
                        href={whatsappUrl}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="text-sm text-stone-600 underline"
                    >
                        Confirmer sur WhatsApp
                    </a>
                </div>
            </section>
        </ShopLayout>
    );
}
