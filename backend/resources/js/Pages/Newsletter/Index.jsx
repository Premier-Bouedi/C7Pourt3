import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function NewsletterIndex({ subscribers, stats }) {
  return (
    <AdminLayout>
      <Head title="Gestion de la Newsletter" />

      <div className="space-y-8">
        {/* Page Header */}
        <div className="border-b border-stone-200 pb-6">
          <h1 className="text-3xl font-bold text-stone-900">Newsletter</h1>
          <p className="mt-1 text-sm text-stone-600">Gérez vos abonnés et vos campagnes</p>
        </div>

        {/* Statistics Cards */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div className="bg-white rounded-2xl border border-stone-200 p-6">
            <p className="text-sm font-medium text-stone-600">Abonnés Totaux</p>
            <p className="mt-2 text-3xl font-bold text-blue-900">{stats.total}</p>
          </div>
          <div className="bg-white rounded-2xl border border-stone-200 p-6">
            <p className="text-sm font-medium text-stone-600">Actifs</p>
            <p className="mt-2 text-3xl font-bold text-green-600">{stats.active}</p>
          </div>
          <div className="bg-white rounded-2xl border border-stone-200 p-6">
            <p className="text-sm font-medium text-stone-600">Ce Mois</p>
            <p className="mt-2 text-3xl font-bold text-amber-600">{stats.this_month}</p>
          </div>
        </div>

        {/* Subscribers Table */}
        <div className="bg-white rounded-2xl border border-stone-200 overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-stone-50 border-b border-stone-200">
                <tr>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Email</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Source</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Statut</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Date d'Abonnement</th>
                  <th className="px-6 py-3 text-left text-sm font-semibold text-stone-900">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-stone-200">
                {subscribers.data.map((subscriber) => (
                  <tr key={subscriber.id} className="hover:bg-stone-50 transition-colors">
                    <td className="px-6 py-4 text-sm font-medium text-stone-900">
                      {subscriber.email}
                    </td>
                    <td className="px-6 py-4 text-sm text-stone-600">
                      {subscriber.source === 'footer' && 'Pied de page'}
                      {subscriber.source === 'popup' && 'Pop-up'}
                      {subscriber.source === 'import' && 'Import manuel'}
                    </td>
                    <td className="px-6 py-4">
                      <span className={`inline-block px-3 py-1 rounded-full text-xs font-semibold ${
                        subscriber.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                      }`}>
                        {subscriber.is_active ? 'Actif' : 'Désabonné'}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-sm text-stone-600">
                      {new Date(subscriber.subscribed_at).toLocaleDateString('fr-MA')}
                    </td>
                    <td className="px-6 py-4 text-sm">
                      {subscriber.is_active ? (
                        <button className="text-red-600 hover:underline font-medium">Désabonner</button>
                      ) : (
                        <button className="text-green-600 hover:underline font-medium">Réabonner</button>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* Pagination */}
          {subscribers.last_page > 1 && (
            <div className="bg-stone-50 px-6 py-4 border-t border-stone-200 flex items-center justify-between">
              <p className="text-sm text-stone-600">
                Page {subscribers.current_page} sur {subscribers.last_page}
              </p>
            </div>
          )}
        </div>
      </div>
    </AdminLayout>
  );
}
