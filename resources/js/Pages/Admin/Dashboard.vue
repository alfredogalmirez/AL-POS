<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Define the stats coming from your Controller
const props = defineProps({
    totalProducts: Number,
    lowStock: Number,
    totalSales: Number,
});

// Helper for currency formatting
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(value);
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <div class="p-6 bg-slate-50 min-h-full">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-3xl font-black text-slate-800 mb-8">Admin Dashboard</h1>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                        <p class="text-slate-500 font-bold uppercase text-xs tracking-wider">Total Products</p>
                        <h2 class="text-4xl font-black text-slate-900 mt-2">{{ totalProducts }}</h2>
                    </div>

                    <div :class="[
                        'p-6 rounded-[2rem] shadow-lg text-white',
                        lowStock > 0 ? 'bg-red-500 shadow-red-100' : 'bg-blue-600 shadow-blue-100'
                    ]">
                        <p class="font-bold uppercase text-xs tracking-wider" :class="lowStock > 0 ? 'text-red-100' : 'text-blue-100'">
                            Low Stock Alerts
                        </p>
                        <h2 class="text-4xl font-black mt-2">{{ lowStock }}</h2>
                    </div>

                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
                        <p class="text-slate-500 font-bold uppercase text-xs tracking-wider">Total Sales</p>
                        <h2 class="text-4xl font-black text-slate-900 mt-2">
                            {{ formatCurrency(totalSales) }}
                        </h2>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <h3 class="text-xl font-bold text-slate-800">Inventory Management</h3>
                        <Link
                            :href="route('admin.products.index')"
                            class="bg-slate-900 text-white px-6 py-3 rounded-2xl font-bold hover:bg-black transition-all active:scale-95"
                        >
                            Manage Products
                        </Link>
                    </div>
                    <p class="text-slate-500">Update pricing, adjust stock levels, and add new items to your POS menu.</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
