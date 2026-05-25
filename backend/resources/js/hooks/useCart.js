import { useCallback, useEffect, useState } from 'react';

const KEY = 'c7_cart';

function load() {
    try {
        const r = sessionStorage.getItem(KEY);
        return r ? JSON.parse(r) : [];
    } catch {
        return [];
    }
}

function save(items) {
    sessionStorage.setItem(KEY, JSON.stringify(items));
}

export function useCart() {
    const [items, setItems] = useState([]);

    useEffect(() => {
        setItems(load());
    }, []);

    const persist = useCallback((next) => {
        save(next);
        setItems(next);
    }, []);

    const addItem = useCallback((item) => {
        setItems((p) => {
            const n = p.find((i) => i.variantId === item.variantId)
                ? p.map((i) =>
                      i.variantId === item.variantId
                          ? { ...i, quantity: i.quantity + (item.quantity || 1) }
                          : i,
                  )
                : [...p, item];
            save(n);
            return n;
        });
    }, []);

    const removeItem = useCallback((variantId) => {
        setItems((p) => {
            const n = p.filter((i) => i.variantId !== variantId);
            save(n);
            return n;
        });
    }, []);

    const setQuantity = useCallback((variantId, quantity) => {
        if (quantity < 1) {
            removeItem(variantId);
            return;
        }
        setItems((p) => {
            const n = p.map((i) => (i.variantId === variantId ? { ...i, quantity } : i));
            save(n);
            return n;
        });
    }, [removeItem]);

    const clearCart = useCallback(() => {
        save([]);
        setItems([]);
    }, []);

    const total = items.reduce((s, i) => s + i.price * i.quantity, 0);

    return {
        items,
        addItem,
        removeItem,
        setQuantity,
        clearCart,
        itemCount: items.reduce((s, i) => s + i.quantity, 0),
        total,
    };
}
