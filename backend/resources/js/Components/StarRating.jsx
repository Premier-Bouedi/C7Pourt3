export default function StarRating({ rating = 0, count = 0, size = 'sm' }) {
    const stars = [1, 2, 3, 4, 5];
    const icon = size === 'lg' ? 'text-base' : 'text-xs';

    return (
        <div className="flex items-center gap-1.5">
            <div className={`flex gap-0.5 text-amber-500 ${icon}`} aria-hidden>
                {stars.map((n) => (
                    <span key={n}>{n <= Math.round(rating) ? '★' : '☆'}</span>
                ))}
            </div>
            {count > 0 && <span className="text-xs text-stone-500">({count})</span>}
        </div>
    );
}
