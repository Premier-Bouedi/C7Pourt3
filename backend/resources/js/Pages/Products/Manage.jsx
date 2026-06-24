import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function ProductsManage({ products }) {
  const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-MA', {
      style: 'currency',
      currency: 'MAD',
    }).format(amount / 100);
  };

  return (
    <AdminLayout>
      <Head title="Gestion du Catalogue" />

      <div className="space-y-8">
        {/* Page Header */}
        <div className="flex items-center justify-between border-b border-stone-200 pb-6">
          <div>
            <h1 className="text-3xl font-bold text-stone-900">Catalogue Produits</h1>
            <p className="mt-1 text-sm text-stone-600">Gestion complète CRUD des produits luxe</p>
          </div>
          <Link
            href="/admin/products/create"
            className="px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-950 font-medium"
          >
            + Nouveau Produit
          </Link>
        </div>

        {/* Products Table */}
        <div className="bg-white rounded-2xl border border-stone-200 overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-stone-50 border-b border-stone-200">
                <tr>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Image</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Nom</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Catégorie</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Prix</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Statut</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-stone-200">
                {products.map((product) => (
                  <tr key={product.id} className="hover:bg-stone-50 transition-colors">
                    <td className="px-6 py-4">
                      {product.images && product.images.length > 0 ? (
                        <img 
                          src={product.images[0]} 
                          alt={product.name} 
                          className="w-12 h-12 rounded-lg object-cover bg-stone-100"
                        />
                      ) : (
                        <div className="w-12 h-12 rounded-lg bg-stone-200 flex items-center justify-center text-stone-400">
                          <span className="text-xs">Aucune</span>
                        </div>
                      )}
                    </td>
                    <td className="px-6 py-4 text-sm font-medium text-stone-900">
                      {product.name}
                    </td>
                    <td className="px-6 py-4 text-sm text-stone-600">
                      {product.category || '—'}
                    </td>
                    <td className="px-6 py-4 text-sm font-semibold text-stone-900">
                      {formatCurrency(product.base_price)}
                    </td>
                    <td className="px-6 py-4">
                      <span className={`inline-block px-3 py-1 rounded-full text-xs font-semibold ${
                        product.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                      }`}>
                        {product.is_active ? 'Actif' : 'Inactif'}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-sm space-x-3">
                      <Link
                        href={`/admin/products/${product.slug}/edit`}
                        className="text-blue-900 hover:underline font-medium"
                      >
                        Modifier
                      </Link>
                      <button className="text-red-600 hover:underline font-medium">
                        Supprimer
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>


        </div>
      </div>
    </AdminLayout>
  );
}
