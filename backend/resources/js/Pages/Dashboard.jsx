import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Dashboard({ stats, stock, alerts }) {
  return (
    <AdminLayout>
      <Head title="Dashboard" />

      <div className="space-y-8">
        {/* Page Header */}
        <div className="border-b border-stone-200 pb-6">
          <h1 className="text-3xl font-bold text-stone-900">
            Tableau de Bord
          </h1>
          <p className="mt-1 text-sm text-stone-600">
            Vue d'ensemble de votre activité
          </p>
        </div>

        {/* Key Metrics Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {/* Total Orders Card */}
          <div className="bg-white rounded-2xl border border-stone-200 p-6 hover:shadow-lg transition-shadow">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-stone-600">Commandes Totales</p>
                <p className="mt-2 text-3xl font-bold text-stone-900">{stats.total_orders}</p>
              </div>
              <div className="w-12 h-12 bg-blue-900 rounded-full flex items-center justify-center">
                <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
              </div>
            </div>
          </div>

          {/* Pending Orders Card */}
          <div className="bg-white rounded-2xl border border-stone-200 p-6 hover:shadow-lg transition-shadow">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-stone-600">Commandes En Attente</p>
                <p className="mt-2 text-3xl font-bold text-amber-600">{stats.pending_orders}</p>
              </div>
              <div className="w-12 h-12 bg-amber-500 rounded-full flex items-center justify-center">
                <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>

          {/* Total Revenue Card */}
          <div className="bg-white rounded-2xl border border-stone-200 p-6 hover:shadow-lg transition-shadow">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-stone-600">Chiffre d'Affaires</p>
                <p className="mt-2 text-3xl font-bold text-green-600">{(stats.total_revenue / 100000).toFixed(1)}K MAD</p>
              </div>
              <div className="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>

          {/* Active Products Card */}
          <div className="bg-white rounded-2xl border border-stone-200 p-6 hover:shadow-lg transition-shadow">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-stone-600">Produits Actifs</p>
                <p className="mt-2 text-3xl font-bold text-stone-900">{stats.active_products}</p>
              </div>
              <div className="w-12 h-12 bg-stone-700 rounded-full flex items-center justify-center">
                <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20.354 15.354A9 9 0 015.646 5.646 9 9 0 0120.354 15.354z" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        {/* Stock & Alerts Section */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Casablanca Stock */}
          <div className="bg-white rounded-2xl border border-stone-200 p-6">
            <h3 className="text-lg font-semibold text-stone-900 mb-4">Stock Casablanca</h3>
            <p className="text-4xl font-bold text-blue-900">{stock.casablanca}</p>
            <p className="mt-2 text-sm text-stone-600">Unités disponibles</p>
          </div>

          {/* Libreville Stock */}
          <div className="bg-white rounded-2xl border border-stone-200 p-6">
            <h3 className="text-lg font-semibold text-stone-900 mb-4">Stock Libreville</h3>
            <p className="text-4xl font-bold text-emerald-600">{stock.libreville}</p>
            <p className="mt-2 text-sm text-stone-600">Unités disponibles</p>
          </div>

          {/* Transit Alerts */}
          <div className="bg-white rounded-2xl border border-stone-200 p-6">
            <h3 className="text-lg font-semibold text-stone-900 mb-4">Alertes Transit 48h</h3>
            <p className="text-4xl font-bold text-red-600">{alerts.transit_48h}</p>
            <p className="mt-2 text-sm text-stone-600">Commandes à surveiller</p>
          </div>
        </div>

        {/* Quick Actions */}
        <div className="bg-gradient-to-r from-blue-900 to-blue-950 rounded-2xl p-8 text-white">
          <h3 className="text-2xl font-bold mb-6">Actions Rapides</h3>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <Link
              href="/admin/orders"
              className="bg-white/10 hover:bg-white/20 rounded-xl p-4 transition-colors text-center font-medium"
            >
              → Gérer les Commandes
            </Link>
            <Link
              href="/admin/products"
              className="bg-white/10 hover:bg-white/20 rounded-xl p-4 transition-colors text-center font-medium"
            >
              → Catalogue Produits
            </Link>
            <Link
              href="/admin/cod-payments"
              className="bg-white/10 hover:bg-white/20 rounded-xl p-4 transition-colors text-center font-medium"
            >
              → Valider Paiements
            </Link>
          </div>
        </div>
      </div>
    </AdminLayout>
  );
}
