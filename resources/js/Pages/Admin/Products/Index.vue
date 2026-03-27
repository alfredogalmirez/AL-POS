<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    products: Object, // Laravel Paginated Object
});

// Helper for currency formatting (PHP/Pesos)
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(value);
};

// Handle Delete with Inertia
const deleteProduct = (id) => {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        router.delete(route('admin.products.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                // Optional: I'll add a success notification here later
            }
        });
    }
};
</script>

<template>

    <Head title="Inventory Management" />

    <AdminLayout>
        <div class="p-8 bg-slate-50 min-h-full">
            <div class="max-w-7xl mx-auto">

                <div class="flex flex-col sm:flex-row justify-between items-end sm:items-center gap-6 mb-10 px-2">
                    <div class="space-y-1">
                        <h1 class="text-4xl font-black text-slate-900 tracking-tighter">Inventory</h1>
                        <p class="text-slate-500">View and manage your product stock list.</p>
                    </div>
                    <Link :href="route('admin.products.create')"
                        class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all active:scale-95 text-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Add New Product
                    </Link>
                </div>

                <div class="bg-white rounded-[3rem] shadow-sm border border-slate-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[1100px] table-fixed">
                            <thead class="bg-slate-50/50">
                                <tr class="border-b border-slate-100">
                                    <th
                                        class="w-[45%] px-14 py-6 text-slate-500 font-bold uppercase text-xs tracking-wider">
                                        Product</th>
                                    <th
                                        class="w-[45%] px-14 py-6 text-slate-500 font-bold uppercase text-xs tracking-wider">
                                        Price</th>
                                    <th
                                        class="w-[45%] px-14 py-6 text-slate-500 font-bold uppercase text-xs tracking-wider">
                                        Stock Status</th>
                                    <th
                                        class="w-[45%] px-14 py-6 text-slate-500 font-bold uppercase text-xs tracking-wider text-right">
                                        Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="product in products.data" :key="product.id"
                                    class="hover:bg-slate-50/30 transition-colors group">

                                    <td class="px-12 py-8 font-medium text-slate-800">
                                        <div class="flex items-center gap-5">
                                            <div
                                                class="w-20 h-20 rounded-2xl overflow-hidden bg-slate-100 border border-slate-100 flex-shrink-0">
                                                <img v-if="product.image" :src="`/storage/${product.image}`"
                                                    class="w-full h-full object-cover transition-transform group-hover:scale-110"
                                                    :alt="product.name">
                                                <div v-else
                                                    class="w-full h-full flex items-center justify-center text-slate-300">
                                                    <svg class="w-10 h-10" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="1"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="font-black text-slate-900 text-lg leading-tight">{{
                                                    product.name }}</p>
                                                <p class="text-xs text-slate-400 mt-1 uppercase tracking-wider">SKU: {{
                                                    String(product.id).padStart(5, '0') }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-10 py-6 text-slate-700 font-medium text-lg">
                                        {{ formatCurrency(product.price) }}
                                    </td>

                                    <td class="px-10 py-6">
                                        <span :class="[
                                            'px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider',
                                            product.stock < 5 ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'
                                        ]">
                                            {{ product.stock }} in stock
                                        </span>
                                    </td>

                                    <td class="px-10 py-6 text-right">
                                        <div class="inline-flex items-center gap-3">
                                            <Link :href="route('admin.products.edit', product.id)"
                                                class="p-2.5 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-xl transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </Link>

                                            <button @click="deleteProduct(product.id)"
                                                class="p-2.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="products.data.length === 0">
                                    <td colspan="4" class="px-8 py-24 text-center">
                                        <div class="flex flex-col items-center justify-center max-w-md mx-auto">
                                            <div
                                                class="text-slate-300 mb-6 bg-slate-100 p-6 rounded-full border-2 border-dashed border-slate-200">
                                                <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1"
                                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                </svg>
                                            </div>
                                            <h3 class="text-2xl font-black text-slate-900 leading-tight">Your inventory
                                                is currently empty</h3>
                                            <p class="text-slate-500 text-base mt-2 mb-8">It looks like you haven’t
                                                added any products yet. Get started by adding your first item to the
                                                system.</p>
                                            <Link :href="route('admin.products.create')"
                                                class="bg-blue-600 text-white px-7 py-3 rounded-2xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all active:scale-95 text-lg">
                                                + Add Your First Product
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="p-10 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-slate-500 font-medium">
                            Showing {{ products.from || 0 }} to {{ products.to || 0 }} of {{ products.total || 0 }}
                            products
                        </p>
                        <div class="flex items-center gap-1.5">
                            <Link v-for="link in products.links" :key="link.label" :href="link.url || '#'"
                                v-html="link.label"
                                class="px-5 py-2.5 rounded-xl text-sm font-black transition-all border" :class="{
                                    'bg-blue-600 text-white border-blue-600 shadow-md': link.active,
                                    'bg-white text-slate-600 border-slate-100 hover:bg-slate-50 hover:border-slate-200': !link.active && link.url,
                                    'text-slate-300 border-slate-50 pointer-events-none': !link.url
                                }" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
