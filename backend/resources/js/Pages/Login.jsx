import { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import ShopLayout from '../Layouts/ShopLayout';

export default function Login({ errors, old }) {
    const form = useForm({
        email: old?.email ?? '',
        password: '',
        remember: false,
    });

    const submit = (event) => {
        event.preventDefault();
        form.post('/login');
    };

    return (
        <ShopLayout>
            <section className="mx-auto max-w-lg px-4 py-16">
                <div className="rounded-3xl border border-stone-200 bg-white p-10 shadow-sm">
                    <header className="mb-8 text-center">
                        <p className="text-sm uppercase tracking-[0.3em] text-stone-500">C7Pourt3</p>
                        <h1 className="mt-4 text-3xl font-semibold text-stone-900">Sign in</h1>
                    </header>

                    <form onSubmit={submit} className="space-y-6">
                        <div>
                            <label className="mb-2 block text-sm font-medium text-stone-700">Email address *</label>
                            <input
                                name="email"
                                type="email"
                                value={form.data.email}
                                onChange={(event) => form.setData('email', event.target.value)}
                                className="w-full rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm text-stone-900 outline-none transition focus:border-stone-900 focus:bg-white"
                                required
                                autoComplete="email"
                            />
                            {errors?.email && <p className="mt-2 text-sm text-rose-600">{errors.email}</p>}
                        </div>

                        <div>
                            <label className="mb-2 block text-sm font-medium text-stone-700">Password *</label>
                            <input
                                name="password"
                                type="password"
                                value={form.data.password}
                                onChange={(event) => form.setData('password', event.target.value)}
                                className="w-full rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm text-stone-900 outline-none transition focus:border-stone-900 focus:bg-white"
                                required
                                autoComplete="current-password"
                            />
                            {errors?.password && <p className="mt-2 text-sm text-rose-600">{errors.password}</p>}
                        </div>

                        <div className="flex items-center justify-between gap-3">
                            <label className="inline-flex items-center gap-2 text-sm text-stone-700">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    checked={form.data.remember}
                                    onChange={(event) => form.setData('remember', event.target.checked)}
                                    className="h-4 w-4 rounded border-stone-300 text-stone-900 focus:ring-stone-900"
                                />
                                Remember me
                            </label>

                            <Link href="/" className="text-sm text-stone-500 hover:text-stone-900">
                                Back
                            </Link>
                        </div>

                        <button
                            type="submit"
                            disabled={form.processing}
                            className="w-full rounded-2xl bg-stone-900 px-4 py-3 text-sm font-semibold uppercase tracking-[0.24em] text-white transition hover:bg-stone-700 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                            Sign in
                        </button>
                    </form>
                </div>
            </section>
        </ShopLayout>
    );
}
