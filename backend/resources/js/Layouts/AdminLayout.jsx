import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';

export default function AdminLayout({ children }) {
  const [sidebarOpen, setSidebarOpen] = useState(true);
  const { auth, stats, stock } = usePage().props ?? {};
  // Use the browser's pathname for active link detection
  const isActive = (path) => window.location.pathname === path;
  const totalStock = (stock?.casablanca ?? 0) + (stock?.libreville ?? 0);

  const menuItems = [
    { icon: '📊', label: 'Tableau de Bord', href: '/admin/dashboard', key: 'dashboard' },
    { icon: '📦', label: 'Commandes', href: '/admin/orders', key: 'orders.index' },
    { icon: '💳', label: 'Paiements', href: '/admin/cod-payments', key: 'payments.cod.index' },
    { icon: '🛍️', label: 'Produits', href: '/admin/products', key: 'products.manage', badge: stats?.active_products },
    { icon: '📦', label: 'Stock', href: '/admin/stock', key: 'stock.index', badge: totalStock },
    { icon: '⭐', label: 'Avis Clients', href: '/admin/reviews', key: 'reviews.index' },
  ];

  return (
    <div className="flex h-screen bg-stone-50">
      {/* Sidebar */}
      <aside
        className={`bg-gradient-to-b from-blue-950 to-blue-900 text-white transition-all duration-300 ${
          sidebarOpen ? 'w-64' : 'w-20'
        } fixed inset-y-0 left-0 z-50 overflow-y-auto`}
      >
        {/* Logo */}
        <div className="flex items-center justify-between p-4 border-b border-blue-800">
          {sidebarOpen && (
  <Link href="/admin/dashboard" className="text-2xl font-bold text-white hover:opacity-80 transition-opacity">
              C7Pourt3
            </Link>
          )}
          <button
            onClick={() => setSidebarOpen(!sidebarOpen)}
            className="p-2 hover:bg-blue-800 rounded-lg transition-colors"
            aria-label="Toggle sidebar"
          >
            {sidebarOpen ? '◀' : '▶'}
          </button>
        </div>

        {/* Navigation Menu */}
        <nav className="mt-8 space-y-2 px-3">
          {menuItems.map((item) => (
            <Link
              key={item.key}
              href={item.href}
              className={`flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 ${
                isActive(item.href)
                  ? 'bg-white/20 text-white font-semibold'
                  : 'text-blue-100 hover:bg-white/10'
              }`}
            >
              <span className="text-lg">{item.icon}</span>
                {sidebarOpen && item.badge !== undefined && (
                  <span className="ml-2 text-sm text-gray-300">({item.badge})</span>
                )}
                {sidebarOpen && <span>{item.label}</span>}
            </Link>
          ))}
        </nav>

        {/* User Profile Section */}
        <div className="absolute bottom-0 left-0 right-0 border-t border-blue-800 p-4">
          <div className={`flex items-center ${sidebarOpen ? 'space-x-3' : 'justify-center'}`}>
            <div className="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center font-bold">
              {auth?.user?.name?.charAt(0)}
            </div>
            {sidebarOpen && (
              auth?.user?.name ? (
                <div className="flex-1">
                  <p className="text-sm font-semibold text-white truncate">{auth.user.name}</p>
                  <p className="text-xs text-blue-200">{auth.user.role}</p>
                </div>
              ) : null
            )}
          </div>
          {sidebarOpen && (
            <form method="POST" action="/logout" className="mt-3">
              <button
                type="submit"
                className="w-full text-sm text-left px-2 py-2 text-blue-100 hover:text-white transition-colors"
              >
                Déconnexion
              </button>
            </form>
          )}
        </div>
      </aside>

      {/* Main Content */}
      <main className={`flex-1 flex flex-col overflow-hidden transition-all duration-300 ${sidebarOpen ? 'ml-64' : 'ml-20'}`}>
        {/* Top Navigation Bar */}
        <header className="bg-white border-b border-stone-200 px-8 py-4 shadow-sm">
          <div className="flex items-center justify-between">
            <h2 className="text-lg font-semibold text-stone-900">
              Administration C7Pourt3
            </h2>
            <div className="flex items-center space-x-4">
              <span className="text-sm text-stone-600">{new Date().toLocaleDateString('fr-MA')}</span>
            </div>
          </div>
        </header>

        {/* Scrollable Content Area */}
        <div className="flex-1 overflow-y-auto">
          <div className="p-8">
            {children}
          </div>
        </div>
      </main>
    </div>
  );
}
