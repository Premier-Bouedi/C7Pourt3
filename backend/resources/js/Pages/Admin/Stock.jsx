import React from 'react';
import { Head, usePage } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Stock() {
  const { stock } = usePage().props ?? {};
  const casablanca = stock?.casablanca ?? 0;
  const libreville = stock?.libreville ?? 0;

  return (
    <AdminLayout>
      <Head title="Stock Produits" />
      <div className="p-8 space-y-8 bg-gradient-to-b from-blue-900 to-blue-950 min-h-screen text-white rounded-2xl">
        <h1 className="text-3xl font-bold mb-6">Stock des Produits</h1>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {/* Casablanca Stock Card */}
          <div className="bg-white/10 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-white/10">
            <h2 className="text-xl font-semibold mb-2 text-blue-200">Casablanca</h2>
            <p className="text-4xl font-bold text-blue-100">{casablanca}</p>
            <p className="text-sm text-blue-300 mt-1">unités disponibles</p>
          </div>
          {/* Libreville Stock Card */}
          <div className="bg-white/10 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-white/10">
            <h2 className="text-xl font-semibold mb-2 text-emerald-200">Libreville</h2>
            <p className="text-4xl font-bold text-emerald-100">{libreville}</p>
            <p className="text-sm text-emerald-300 mt-1">unités disponibles</p>
          </div>
        </div>
      </div>
    </AdminLayout>
  );
}
