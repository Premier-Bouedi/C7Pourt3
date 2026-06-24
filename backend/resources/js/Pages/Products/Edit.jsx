import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function ProductsEdit({ product }) {
  const { data, setData, put, errors } = useForm({
    name: product.name || '',
    slug: product.slug || '',
    description: product.description || '',
    base_price: product.base_price || '',
    compare_at_price: product.compare_at_price || '',
    category: product.category || '',
    is_active: product.is_active || true,
    is_featured: product.is_featured || false,
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    put(`/admin/products/${product.slug}`);
  };

  return (
    <AdminLayout>
      <Head title={`Modifier: ${product.name}`} />

      <div className="space-y-8">
        {/* Page Header */}
        <div className="border-b border-stone-200 pb-6">
          <h1 className="text-3xl font-bold text-stone-900">Modifier Produit</h1>
          <p className="mt-1 text-sm text-stone-600">{product.name}</p>
        </div>

        {/* Form */}
        <form onSubmit={handleSubmit} className="bg-white rounded-2xl border border-stone-200 p-8 space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* Product Name */}
            <div>
              <label className="block text-sm font-semibold text-stone-900 mb-2">
                Nom du Produit *
              </label>
              <input
                type="text"
                value={data.name}
                onChange={(e) => setData('name', e.target.value)}
                className="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-blue-900 focus:border-transparent"
                placeholder="Sac de luxe..."
              />
              {errors.name && <p className="text-red-600 text-sm mt-1">{errors.name}</p>}
            </div>

            {/* Product Slug */}
            <div>
              <label className="block text-sm font-semibold text-stone-900 mb-2">
                Slug *
              </label>
              <input
                type="text"
                value={data.slug}
                onChange={(e) => setData('slug', e.target.value)}
                className="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-blue-900 focus:border-transparent"
                placeholder="sac-luxe-001"
              />
              {errors.slug && <p className="text-red-600 text-sm mt-1">{errors.slug}</p>}
            </div>

            {/* Base Price */}
            <div>
              <label className="block text-sm font-semibold text-stone-900 mb-2">
                Prix Base (en centimes) *
              </label>
              <input
                type="number"
                value={data.base_price}
                onChange={(e) => setData('base_price', e.target.value)}
                className="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-blue-900 focus:border-transparent"
                placeholder="50000"
              />
              {errors.base_price && <p className="text-red-600 text-sm mt-1">{errors.base_price}</p>}
            </div>

            {/* Compare Price */}
            <div>
              <label className="block text-sm font-semibold text-stone-900 mb-2">
                Prix Comparaison
              </label>
              <input
                type="number"
                value={data.compare_at_price}
                onChange={(e) => setData('compare_at_price', e.target.value)}
                className="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-blue-900 focus:border-transparent"
                placeholder="60000"
              />
            </div>

            {/* Category */}
            <div>
              <label className="block text-sm font-semibold text-stone-900 mb-2">
                Catégorie
              </label>
              <input
                type="text"
                value={data.category}
                onChange={(e) => setData('category', e.target.value)}
                className="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-blue-900 focus:border-transparent"
                placeholder="Sacs à main"
              />
            </div>

            {/* Status */}
            <div>
              <label className="block text-sm font-semibold text-stone-900 mb-2">
                Statut
              </label>
              <select
                value={data.is_active ? 'active' : 'inactive'}
                onChange={(e) => setData('is_active', e.target.value === 'active')}
                className="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-blue-900 focus:border-transparent"
              >
                <option value="active">Actif</option>
                <option value="inactive">Inactif</option>
              </select>
            </div>
          </div>

          {/* Description */}
          <div>
            <label className="block text-sm font-semibold text-stone-900 mb-2">
              Description
            </label>
            <textarea
              value={data.description}
              onChange={(e) => setData('description', e.target.value)}
              className="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-blue-900 focus:border-transparent h-32"
              placeholder="Description détaillée du produit..."
            />
          </div>

          {/* Checkboxes */}
          <div className="space-y-3">
            <label className="flex items-center space-x-3 cursor-pointer">
              <input
                type="checkbox"
                checked={data.is_featured}
                onChange={(e) => setData('is_featured', e.target.checked)}
                className="w-4 h-4"
              />
              <span className="text-stone-900">Produit en vedette</span>
            </label>
          </div>

          {/* Submit Buttons */}
          <div className="flex gap-4 pt-6 border-t border-stone-200">
            <Link
              href="/products"
              className="px-6 py-2 bg-stone-200 text-stone-900 rounded-lg hover:bg-stone-300 font-medium"
            >
              Annuler
            </Link>
            <button
              type="submit"
              className="px-6 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-950 font-medium"
            >
              Mettre à jour
            </button>
          </div>
        </form>
      </div>
    </AdminLayout>
  );
}
