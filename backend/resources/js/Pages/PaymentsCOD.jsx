import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function PaymentsCOD({ payments, summary }) {
  const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-MA', {
      style: 'currency',
      currency: 'MAD',
    }).format(amount / 100);
  };

  const getStatusBadge = (status) => {
    const badges = {
      pending: 'bg-amber-100 text-amber-800',
      completed: 'bg-green-100 text-green-800',
      failed: 'bg-red-100 text-red-800',
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
  };

  return (
    <AdminLayout>
      <Head title="Paiements - Espèces à la Livraison" />

      <div className="space-y-8">
        {/* Page Header */}
        <div className="border-b border-stone-200 pb-6">
          <h1 className="text-3xl font-bold text-stone-900">
            Paiements - Espèces à la Livraison
          </h1>
          <p className="mt-1 text-sm text-stone-600">
            Gérez et validez tous les paiements Cash On Delivery
          </p>
        </div>

        {/* Summary Cards */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div className="bg-white rounded-2xl border border-stone-200 p-6">
            <p className="text-sm font-medium text-stone-600">Montant en Attente</p>
            <p className="mt-2 text-3xl font-bold text-amber-600">
              {formatCurrency(summary.pending_amount)}
            </p>
          </div>
          <div className="bg-white rounded-2xl border border-stone-200 p-6">
            <p className="text-sm font-medium text-stone-600">Montant Validé</p>
            <p className="mt-2 text-3xl font-bold text-green-600">
              {formatCurrency(summary.completed_amount)}
            </p>
          </div>
          <div className="bg-white rounded-2xl border border-stone-200 p-6">
            <p className="text-sm font-medium text-stone-600">Montant Échoué</p>
            <p className="mt-2 text-3xl font-bold text-red-600">
              {formatCurrency(summary.failed_amount)}
            </p>
          </div>
        </div>

        {/* Payments Table */}
        <div className="bg-white rounded-2xl border border-stone-200 overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-stone-50 border-b border-stone-200">
                <tr>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Référence</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Montant</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Statut</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Date</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-stone-200">
                {payments.data.map((payment) => (
                  <tr key={payment.id} className="hover:bg-stone-50 transition-colors">
                    <td className="px-6 py-4 text-sm font-medium text-stone-900">
                      {payment.order.reference}
                    </td>
                    <td className="px-6 py-4 text-sm font-semibold text-stone-900">
                      {formatCurrency(payment.amount)}
                    </td>
                    <td className="px-6 py-4">
                      <span className={`inline-block px-3 py-1 rounded-full text-xs font-semibold ${getStatusBadge(payment.status)}`}>
                        {payment.status === 'pending' && 'En Attente'}
                        {payment.status === 'completed' && 'Validé'}
                        {payment.status === 'failed' && 'Échoué'}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-sm text-stone-600">
                      {new Date(payment.created_at).toLocaleDateString('fr-MA')}
                    </td>
                    <td className="px-6 py-4 text-sm">
                      {payment.status === 'pending' && (
                        <button className="text-blue-900 hover:underline font-medium">
                          Valider
                        </button>
                      )}
                      {payment.status !== 'pending' && (
                        <span className="text-stone-400">—</span>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* Pagination */}
          {payments.last_page > 1 && (
            <div className="bg-stone-50 px-6 py-4 border-t border-stone-200 flex items-center justify-between">
              <p className="text-sm text-stone-600">
                Page {payments.current_page} sur {payments.last_page}
              </p>
              <div className="space-x-2">
                {payments.current_page > 1 && (
                  <Link href={payments.first_page_url} className="px-3 py-1 bg-white border border-stone-200 rounded text-sm hover:bg-stone-50">
                    Première
                  </Link>
                )}
                {payments.next_page_url && (
                  <Link href={payments.next_page_url} className="px-3 py-1 bg-blue-900 text-white rounded text-sm hover:bg-blue-950">
                    Suivant
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
