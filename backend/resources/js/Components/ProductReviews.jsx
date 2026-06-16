import { Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import StarRating from './StarRating';

export default function ProductReviews({ productSlug, productName }) {
    const [reviews, setReviews] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        if (!productSlug) {
            return;
        }
        setLoading(true);
        fetch(`/api/products/${productSlug}/reviews`)
            .then((r) => r.json())
            .then((d) => setReviews(d.reviews ?? []))
            .finally(() => setLoading(false));
    }, [productSlug]);

    const avisHref = productSlug ? `/avis?product=${productSlug}` : '/avis';

    return (
        <div className="mt-8 border-t border-stone-200 pt-6">
            <div className="flex flex-wrap items-center justify-between gap-3 border-b border-stone-200 pb-2">
                <p className="text-xs font-medium uppercase tracking-widest text-stone-900">
                    Avis clients {reviews.length > 0 && `(${reviews.length})`}
                </p>
                <Link
                    href={avisHref}
                    className="text-[11px] font-medium uppercase tracking-wider text-stone-600 underline hover:text-stone-900"
                >
                    Laisser un avis
                </Link>
            </div>

            <div className="mt-4">
                {loading && <p className="text-sm text-stone-400">Chargement…</p>}
                {!loading && reviews.length === 0 && (
                    <p className="text-sm text-stone-500">
                        Aucun avis publié pour {productName ? `« ${productName} »` : 'ce sac'} pour le moment.
                    </p>
                )}
                <ul className="space-y-4">
                    {reviews.map((r) => (
                        <li key={r.id} className="rounded border border-stone-100 bg-stone-50/50 p-4">
                            <div className="flex flex-wrap items-center gap-2">
                                <StarRating rating={r.rating} size="sm" />
                                <span className="font-medium text-stone-900">{r.author_name}</span>
                                {r.is_verified_purchase && (
                                    <span className="rounded border border-amber-200 bg-amber-50 px-2 py-0.5 text-[10px] font-medium uppercase tracking-wider text-amber-800">
                                        ✓ Achat vérifié
                                    </span>
                                )}
                            </div>
                            {r.comment && <p className="mt-2 text-sm leading-relaxed text-stone-600">{r.comment}</p>}
                        </li>
                    ))}
                </ul>
                <p className="mt-4 text-[11px] text-stone-400">
                    Commande livrée requise — badge achat vérifié. Avis modérés avant publication.
                </p>
                <Link
                    href={avisHref}
                    className="mt-3 inline-block text-xs font-medium uppercase tracking-wider text-stone-900 underline"
                >
                    Donner mon avis sur ce sac
                </Link>
            </div>
        </div>
    );
}
