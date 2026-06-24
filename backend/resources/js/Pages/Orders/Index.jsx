import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function OrdersIndex({ orders }) {
  const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-MA', {
      style: 'currency',
      currency: 'MAD',
    }).format(amount / 100);
  };

  const getStatusBadge = (status) => {
    const badges = {
      pending: 'bg-yellow-100 text-yellow-800',
      confirmed: 'bg-blue-100 text-blue-800',
      processing: 'bg-indigo-100 text-indigo-800',
      shipped: 'bg-purple-100 text-purple-800',
      in_transit: 'bg-cyan-100 text-cyan-800',
      delivered: 'bg-green-100 text-green-800',
      cancelled: 'bg-red-100 text-red-800',
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
  };

  const getStatusLabel = (status) => {
    const labels = {
      pending: 'En Attente',
      confirmed: 'Confirmée',
      processing: 'En Traitement',
      shipped: 'Expédiée',
      in_transit: 'En Transit',
      delivered: 'Livrée',
      cancelled: 'Annulée',
    };
    return labels[status] || status;
  };

  return (
    <AdminLayout>
      <Head title="Gestion des Commandes" />

      <div className="space-y-8">
        {/* Page Header */}
        <div className="border-b border-stone-200 pb-6">
          <h1 className="text-3xl font-bold text-stone-900">Gestion des Commandes</h1>
          <p className="mt-1 text-sm text-stone-600">Suivi et gestion des commandes clients</p>
        </div>

        {/* Orders Table */}
        <div className="bg-white rounded-2xl border border-stone-200 overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-stone-50 border-b border-stone-200">
                <tr>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Référence</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Client</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Montant</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Statut</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Date</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-stone-200">
                {orders.data.map((order) => (
                  <tr key={order.id} className="hover:bg-stone-50 transition-colors">
                    <td className="px-6 py-4 text-sm font-medium text-blue-900">
                      {order.reference}
                    </td>
                    <td className="px-6 py-4 text-sm text-stone-900">
                      {order.customer_name}
                    </td>
                    <td className="px-6 py-4 text-sm font-semibold text-stone-900">
                      {formatCurrency(order.total)}
                    </td>
                    <td className="px-6 py-4">
                      <span className={`inline-block px-3 py-1 rounded-full text-xs font-semibold ${getStatusBadge(order.status)}`}>
                        {getStatusLabel(order.status)}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-sm text-stone-600">
                      {new Date(order.created_at).toLocaleDateString('fr-MA')}
                    </td>
                    <td className="px-6 py-4 text-sm">
                      <Link
                        href={`/admin/orders/${order.id}`}
                        className="text-blue-900 hover:underline font-medium"
                      >
                        Voir →
                      </Link>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* Pagination */}
          {orders.last_page > 1 && (
            <div className="bg-stone-50 px-6 py-4 border-t border-stone-200 flex items-center justify-between">
              <p className="text-sm text-stone-600">
                Page {orders.current_page} sur {orders.last_page}
              </p>
              <div className="space-x-2">
                {orders.current_page > 1 && (
                  <Link href={orders.first_page_url} className="px-3 py-1 bg-white border border-stone-200 rounded text-sm hover:bg-stone-50">
                    ← Première
                  </Link>
                )}
                {orders.next_page_url && (
                  <Link href={orders.next_page_url} className="px-3 py-1 bg-blue-900 text-white rounded text-sm hover:bg-blue-950">
                    Suivant →
                  </Link>
                )}
              </div>
            </div>
          )}
        </div>
      </div>
    </AdminLayout>
  );
}
