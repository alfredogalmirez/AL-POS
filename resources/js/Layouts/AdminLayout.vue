<script setup>
import { ref, onMounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import Toast from '@/Components/Toast.vue';

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div class="flex min-h-screen bg-slate-50 text-slate-900">
        <Toast />

        <aside class="w-72 bg-white border-r border-slate-100 flex flex-col sticky top-0 h-screen">
            <div class="p-8">
                <h1 class="text-2xl font-black tracking-tighter text-blue-600">AL-POS</h1>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Admin Panel</p>
            </div>

            <nav class="flex-1 px-4 space-y-2">
                <Link :href="route('admin.dashboard')" :class="[
                    'block px-4 py-4 rounded-2xl font-bold transition-all',
                    route().current('admin.dashboard')
                        ? 'bg-blue-50 text-blue-600 px-6'
                        : 'text-slate-500 hover:bg-slate-50'
                ]">
                    Dashboard
                </Link>

                <div class="pt-4">
                    <p class="px-6 py-2 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Management</p>
                    <Link :href="route('admin.products.index')" :class="['block px-4 py-4 rounded-2xl font-bold transition-all',
                        route().current('admin.products.index')
                            ? 'bg-blue-50 text-blue-600 px-6'
                            : 'text-slate-500 hover:bg-slate-50'
                    ]">Inventory</Link>
                    <Link :href="route('admin.categories')" :class="[
                        'block py-4 px-4 rounded-2xl font-bold transition-all',
                        route().current('admin.categories')
                            ? 'bg-blue-50 text-blue-600 px-6'
                            : 'text-slate-500 hover:bg-slate-50'
                    ]">Categories</Link>
                    <Link :href="route('admin.transactions.index')" :class="[
                        'block py-4 px-4 rounded-2xl font-bold transition-all',
                        route().current('admin.transactions.index')
                            ? 'bg-blue-50 text-blue-600 px-6'
                            : 'text-slate-500 hover:bg-slate-50'
                    ]">Transactions</Link>
                </div>
            </nav>

            <div class="p-6 border-t border-slate-50">
                <button @click="logout"
                    class="p-3 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </div>
        </aside>

        <main class="flex-1 p-10 overflow-y-auto">
            <slot />
        </main>
    </div>
</template>
