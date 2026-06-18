import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function OrdersShow({ order }) {
  const formatCurrency = (amount) => {
    return new Intl.NumberFormat('fr-MA', {
      style: 'currency',
      currency: 'MAD',
    }).format(amount / 100);
  };

  const getStatusColor = (status) => {
    const colors = {
      pending: 'text-yellow-600 bg-yellow-50',
      confirmed: 'text-blue-600 bg-blue-50',
      processing: 'text-indigo-600 bg-indigo-50',
      shipped: 'text-purple-600 bg-purple-50',
      in_transit: 'text-cyan-600 bg-cyan-50',
      delivered: 'text-green-600 bg-green-50',
      cancelled: 'text-red-600 bg-red-50',
    };
    return colors[status] || 'text-gray-600 bg-gray-50';
  };

  return (
    <AdminLayout>
      <Head title={`Commande ${order.reference}`} />

      <div className="space-y-8">
        {/* Header */}
        <div className="flex items-center justify-between border-b border-stone-200 pb-6">
          <div>
            <h1 className="text-3xl font-bold text-stone-900">Commande {order.reference}</h1>
            <p className="mt-1 text-sm text-stone-600">
              Créée le {new Date(order.created_at).toLocaleDateString('fr-MA')}
            </p>
          </div>
          <Link href="/orders" className="px-4 py-2 bg-stone-200 text-stone-900 rounded-lg hover:bg-stone-300">
            ← Retour
          </Link>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Main Info */}
          <div className="lg:col-span-2 space-y-6">
            {/* Customer Info */}
            <div className="bg-white rounded-2xl border border-stone-200 p-6">
              <h3 className="text-lg font-semibold text-stone-900 mb-4">Informations Client</h3>
              <p className="text-stone-600">Nom: <strong className="text-stone-900">{order.customer_name}</strong></p>
              <p className="text-stone-600">Téléphone: <strong className="text-stone-900">{order.customer_phone}</strong></p>
              <p className="text-stone-600">Adresse: <strong className="text-stone-900">{order.shipping_address}</strong></p>
            </div>

            {/* Order Items */}
            <div className="bg-white rounded-2xl border border-stone-200 p-6">
              <h3 className="text-lg font-semibold text-stone-900 mb-4">Articles Commandés</h3>
              <div className="space-y-4">
                {order.items.map((item) => (
                  <div key={item.id} className="flex justify-between items-start border-b border-stone-100 pb-4">
                    <div className="flex-1">
                      <p className="font-medium text-stone-900">{item.product_name}</p>
                      <p className="text-sm text-stone-600">Quantité: {item.quantity}</p>
                      {item.variant_color && (
                        <p className="text-sm text-stone-600">Couleur: {item.variant_color}</p>
                      )}
                    </div>
                    <p className="font-semibold text-stone-900">
                      {formatCurrency(item.price_paid * item.quantity)}
                    </p>
                  </div>
                ))}
              </div>
            </div>

            {/* Payments */}
            {order.payments && order.payments.length > 0 && (
              <div className="bg-white rounded-2xl border border-stone-200 p-6">
                <h3 className="text-lg font-semibold text-stone-900 mb-4">Paiements</h3>
                <div className="space-y-3">
                  {order.payments.map((payment) => (
                    <div key={payment.id} className="flex justify-between items-center p-3 bg-stone-50 rounded-lg">
                      <span className="text-stone-900 font-medium">{formatCurrency(payment.amount)}</span>
                      <span className={`px-3 py-1 rounded text-xs font-semibold ${
                        payment.status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800'
                      }`}>
                        {payment.status === 'completed' ? 'Validé' : 'En Attente'}
                      </span>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>

          {/* Sidebar */}
          <div className="space-y-6">
            {/* Status */}
            <div className={`rounded-2xl border border-stone-200 p-6 ${getStatusColor(order.status)}`}>
              <p className="text-sm font-medium mb-2">Statut de la Commande</p>
              <p className="text-2xl font-bold">
                {order.status === 'pending' && 'En Attente'}
                {order.status === 'confirmed' && 'Confirmée'}
                {order.status === 'processing' && 'En Traitement'}
                {order.status === 'shipped' && 'Expédiée'}
                {order.status === 'in_transit' && 'En Transit'}
                {order.status === 'delivered' && 'Livrée'}
                {order.status === 'cancelled' && 'Annulée'}
              </p>
            </div>

            {/* Summary */}
            <div className="bg-white rounded-2xl border border-stone-200 p-6 space-y-4">
              <h3 className="text-lg font-semibold text-stone-900">Résumé</h3>
              <div className="space-y-3 text-sm">
                <div className="flex justify-between">
                  <span className="text-stone-600">Sous-total:</span>
                  <span className="font-medium text-stone-900">{formatCurrency(order.total * 0.8)}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-stone-600">Frais de port:</span>
                  <span className="font-medium text-stone-900">{formatCurrency(order.shipping_fee)}</span>
                </div>
                <div className="flex justify-between pt-3 border-t border-stone-200">
                  <span className="font-semibold text-stone-900">Total:</span>
                  <span className="text-lg font-bold text-blue-900">{formatCurrency(order.total)}</span>
                </div>
              </div>
            </div>

            {/* Delivery Timeline */}
            {order.estimated_delivery_at && (
              <div className="bg-white rounded-2xl border border-stone-200 p-6">
                <h3 className="text-lg font-semibold text-stone-900 mb-3">Livraison Prévue</h3>
                <p className="text-2xl font-bold text-blue-900">
                  {new Date(order.estimated_delivery_at).toLocaleDateString('fr-MA')}
                </p>
              </div>
            )}
          </div>
        </div>
      </div>
    </AdminLayout>
  );
}
