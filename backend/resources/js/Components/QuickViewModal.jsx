import { Dialog, DialogPanel } from '@headlessui/react';
import { useEffect, useMemo, useState } from 'react';
import StarRating from './StarRating';

function formatFcfa(n) {
    return new Intl.NumberFormat('fr-FR').format(n) + ' FCFA';
}

function galleryImages(product, variant) {
    const list = [...(product?.images ?? [])];
    (product?.variants ?? []).forEach((v) => {
        if (v.image && !list.includes(v.image)) {
            list.push(v.image);
        }
    });
    if (variant?.image && !list.includes(variant.image)) {
        list.unshift(variant.image);
    }
    return list.length ? list : variant?.image ? [variant.image] : [];
}

export default function QuickViewModal({ product, open, onClose, onAddToCart }) {
    const variants = product?.variants ?? [];
    const [sel, setSel] = useState(null);
    const [qty, setQty] = useState(1);
    const [activeImage, setActiveImage] = useState(null);

    useEffect(() => {
        if (variants[0]) {
            setSel(variants[0].id);
            setQty(1);
        }
    }, [product?.id, variants]);

    const v = variants.find((x) => x.id === sel) ?? variants[0];
    const price = v?.price ?? product?.base_price;
    const images = useMemo(() => galleryImages(product, v), [product, v]);

    useEffect(() => {
        const main = v?.image || product?.images?.[0] || images[0];
        setActiveImage(main ?? null);
    }, [product?.id, v?.id, images]);

    if (!product) {
        return null;
    }

    const mainImg = activeImage || images[0];
    const sku = v?.sku ?? product.slug?.toUpperCase();

    const addToCart = () => {
        if (!v?.in_stock) {
            return;
        }
        onAddToCart?.({
            variantId: v.id,
            productId: product.id,
            name: product.name,
            color: v.color,
            price,
            image: mainImg,
            quantity: qty,
        });
        onClose();
    };

    return (
        <Dialog open={open} onClose={onClose} className="relative z-50">
            <div className="fixed inset-0 bg-black/55 backdrop-blur-sm" />
            <div className="fixed inset-0 flex items-end sm:items-center sm:justify-center sm:p-6">
                <DialogPanel className="relative max-h-[94vh] w-full max-w-4xl overflow-hidden rounded-t-xl bg-white shadow-2xl sm:rounded-lg">
                    <button
                        type="button"
                        onClick={onClose}
                        className="absolute right-4 top-4 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-white/90 text-xl text-stone-500 shadow hover:text-stone-900"
                        aria-label="Fermer"
                    >
                        ×
                    </button>

                    <div className="grid max-h-[94vh] overflow-y-auto sm:grid-cols-2 sm:overflow-hidden">
                        {/* Galerie — style ALDO */}
                        <div className="bg-stone-100 p-6 sm:overflow-y-auto">
                            <div className="flex aspect-square items-center justify-center bg-white">
                                {mainImg ? (
                                    <img
                                        src={mainImg}
                                        alt={product.name}
                                        className="max-h-full max-w-full object-contain p-4 transition duration-300"
                                    />
                                ) : (
                                    <span className="text-stone-400">Pas d&apos;image</span>
                                )}
                            </div>
                            {images.length > 0 && (
                                <div className="mt-4 flex flex-wrap justify-center gap-2">
                                    {images.map((src) => (
                                        <button
                                            key={src}
                                            type="button"
                                            onClick={() => setActiveImage(src)}
                                            className={`h-16 w-14 shrink-0 overflow-hidden border-2 bg-white transition ${
                                                mainImg === src
                                                    ? 'border-stone-900'
                                                    : 'border-transparent hover:border-stone-300'
                                            }`}
                                        >
                                            <img
                                                src={src}
                                                alt=""
                                                className="h-full w-full object-contain p-1"
                                            />
                                        </button>
                                    ))}
                                </div>
                            )}
                        </div>

                        {/* Infos produit */}
                        <div className="flex flex-col p-6 pb-24 sm:p-8 sm:pb-8">
                            {product.average_rating > 0 && (
                                <StarRating
                                    rating={product.average_rating}
                                    count={product.reviews_count}
                                    size="lg"
                                />
                            )}
                            <h2 className="mt-2 font-serif text-2xl uppercase tracking-wide text-stone-900">
                                {product.name}
                            </h2>
                            <p className="mt-3 text-2xl font-medium text-stone-900">{formatFcfa(price)}</p>
                            <p className="mt-1 text-xs text-stone-500">
                                TTC — livraison Gabon sous ~8 jours · Paiement à la livraison
                            </p>
                            {sku && (
                                <p className="mt-2 text-xs text-stone-400">
                                    Réf. {sku}
                                </p>
                            )}

                            {variants.length > 0 && (
                                <div className="mt-8">
                                    <p className="text-xs font-medium uppercase tracking-widest text-stone-700">
                                        Couleur
                                    </p>
                                    <div className="mt-3 flex flex-wrap gap-3">
                                        {variants.map((x) => (
                                            <button
                                                key={x.id}
                                                type="button"
                                                title={x.color}
                                                onClick={() => {
                                                    setSel(x.id);
                                                    if (x.image) {
                                                        setActiveImage(x.image);
                                                    }
                                                }}
                                                className={`h-9 w-9 rounded-full border-2 transition ring-2 ring-offset-2 ${
                                                    sel === x.id
                                                        ? 'border-stone-900 ring-stone-900'
                                                        : 'border-stone-200 ring-transparent hover:border-stone-400'
                                                }`}
                                                style={{ backgroundColor: x.color_hex || '#d6d3d1' }}
                                            />
                                        ))}
                                    </div>
                                    {v?.color && (
                                        <p className="mt-2 text-sm text-stone-600">{v.color}</p>
                                    )}
                                </div>
                            )}

                            <div className="mt-6">
                                <p className="text-xs font-medium uppercase tracking-widest text-stone-700">
                                    Quantité
                                </p>
                                <div className="mt-2 inline-flex items-center border border-stone-300">
                                    <button
                                        type="button"
                                        onClick={() => setQty((q) => Math.max(1, q - 1))}
                                        className="h-11 w-11 text-lg text-stone-600 hover:bg-stone-50"
                                    >
                                        −
                                    </button>
                                    <span className="w-12 text-center text-sm font-medium">{qty}</span>
                                    <button
                                        type="button"
                                        onClick={() => setQty((q) => q + 1)}
                                        className="h-11 w-11 text-lg text-stone-600 hover:bg-stone-50"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>

                            <button
                                type="button"
                                disabled={!v?.in_stock}
                                onClick={addToCart}
                                className="mt-8 w-full bg-stone-900 py-4 text-sm font-semibold uppercase tracking-[0.2em] text-white transition hover:bg-stone-800 disabled:cursor-not-allowed disabled:opacity-40"
                            >
                                Ajouter au panier
                            </button>

                            <p className="mt-4 flex items-start gap-2 text-xs text-stone-500">
                                <span className="text-base" aria-hidden>
                                    🚚
                                </span>
                                <span>
                                    Expédition depuis le Maroc — livraison estimée 8 jours au Gabon.
                                    Paiement à la réception (COD).
                                </span>
                            </p>

                            {!v?.in_stock && (
                                <p className="mt-2 text-sm font-medium text-red-600">Rupture de stock</p>
                            )}
                        </div>
                    </div>

                    {/* Bouton fixe mobile */}
                    <div className="fixed inset-x-0 bottom-0 border-t border-stone-200 bg-white p-4 sm:hidden">
                        <button
                            type="button"
                            disabled={!v?.in_stock}
                            onClick={addToCart}
                            className="w-full bg-stone-900 py-4 text-sm font-semibold uppercase tracking-[0.2em] text-white disabled:opacity-40"
                        >
                            Ajouter au panier
                        </button>
                    </div>
                </DialogPanel>
            </div>
        </Dialog>
    );
}
