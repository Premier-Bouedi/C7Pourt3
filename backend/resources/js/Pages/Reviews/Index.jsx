import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function ReviewsIndex({ reviews, filters }) {
  const getStatusBadge = (status) => {
    const badges = {
      pending: 'bg-yellow-100 text-yellow-800',
      approved: 'bg-green-100 text-green-800',
      rejected: 'bg-red-100 text-red-800',
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
  };

  const getStatusLabel = (status) => {
    const labels = {
      pending: 'En Attente',
      approved: 'Approuvé',
      rejected: 'Rejeté',
    };
    return labels[status] || status;
  };

  return (
    <AdminLayout>
      <Head title="Gestion des Avis Clients" />

      <div className="space-y-8">
        {/* Page Header */}
        <div className="border-b border-stone-200 pb-6">
          <h1 className="text-3xl font-bold text-stone-900">Avis Clients</h1>
          <p className="mt-1 text-sm text-stone-600">Modération et gestion des avis produits</p>
        </div>

        {/* Reviews Table */}
        <div className="bg-white rounded-2xl border border-stone-200 overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-stone-50 border-b border-stone-200">
                <tr>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Auteur</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Produit</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Note</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Statut</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Date</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-stone-200">
                {reviews.data.map((review) => (
                  <tr key={review.id} className="hover:bg-stone-50 transition-colors">
                    <td className="px-6 py-4 text-sm font-medium text-stone-900">
                      {review.author_name}
                    </td>
                    <td className="px-6 py-4 text-sm text-stone-600">
                      {review.product.name}
                    </td>
                    <td className="px-6 py-4 text-sm">
                      <span className="text-yellow-500">
                        {'⭐'.repeat(review.rating)}
                      </span>
                    </td>
                    <td className="px-6 py-4">
                      <span className={`inline-block px-3 py-1 rounded-full text-xs font-semibold ${getStatusBadge(review.status)}`}>
                        {getStatusLabel(review.status)}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-sm text-stone-600">
                      {new Date(review.created_at).toLocaleDateString('fr-MA')}
                    </td>
                    <td className="px-6 py-4 text-sm space-x-2">
                      {review.status === 'pending' && (
                        <>
                          <button className="text-green-600 hover:underline font-medium">Approuver</button>
                          <button className="text-red-600 hover:underline font-medium">Rejeter</button>
                        </>
                      )}
                      {review.status !== 'pending' && (
                        <span className="text-stone-400">—</span>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* Pagination */}
          {reviews.last_page > 1 && (
            <div className="bg-stone-50 px-6 py-4 border-t border-stone-200 flex items-center justify-between">
              <p className="text-sm text-stone-600">
                Page {reviews.current_page} sur {reviews.last_page}
              </p>
            </div>
          )}
        </div>
      </div>
    </AdminLayout>
  );
}
