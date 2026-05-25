import { useMemo, useState } from 'react';
import StarRating from './StarRating';

function formatFcfa(n) {
    return new Intl.NumberFormat('fr-FR').format(n) + ' FCFA';
}

export default function ProductCard({ product, onQuickView }) {
    const images = useMemo(() => {
        const fromProduct = product.images ?? [];
        const fromVariants = (product.variants ?? [])
            .map((v) => v.image)
            .filter(Boolean);
        return [...new Set([...fromProduct, ...fromVariants])];
    }, [product]);

    const [hovered, setHovered] = useState(false);
    const primary = images[0];
    const secondary = images[1] ?? primary;

    return (
        <article
            className="group"
            onMouseEnter={() => setHovered(true)}
            onMouseLeave={() => setHovered(false)}
        >
            <button
                type="button"
                onClick={() => onQuickView(product)}
                className="relative block w-full overflow-hidden rounded-lg bg-stone-200 aspect-[3/4]"
            >
                {primary && (
                    <img
                        src={primary}
                        alt={product.name}
                        className={`absolute inset-0 h-full w-full object-contain p-2 transition-all duration-500 ease-out ${
                            hovered && images.length > 1
                                ? 'scale-100 opacity-0'
                                : 'scale-100 opacity-100 group-hover:scale-105'
                        }`}
                        loading="lazy"
                    />
                )}
                {images.length > 1 && secondary && (
                    <img
                        src={secondary}
                        alt=""
                        aria-hidden
                        className={`absolute inset-0 h-full w-full object-contain p-2 transition-all duration-500 ease-out ${
                            hovered ? 'scale-105 opacity-100' : 'scale-100 opacity-0'
                        }`}
                        loading="lazy"
                    />
                )}
                <span className="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-stone-900/70 to-transparent p-4 pt-12 text-center text-[10px] font-medium uppercase tracking-[0.25em] text-white opacity-0 transition duration-300 group-hover:opacity-100">
                    Aperçu rapide
                </span>
            </button>

            <div className="mt-3 space-y-1">
                {product.average_rating > 0 && (
                    <StarRating rating={product.average_rating} count={product.reviews_count} />
                )}
                <h3 className="font-serif text-lg leading-snug text-stone-900">{product.name}</h3>
                <div className="flex flex-wrap items-baseline gap-2">
                    <p className="font-medium text-stone-900">{formatFcfa(product.base_price)}</p>
                    {product.compare_at_price > product.base_price && (
                        <p className="text-sm text-stone-400 line-through">
                            {formatFcfa(product.compare_at_price)}
                        </p>
                    )}
                </div>
            </div>
        </article>
    );
}
