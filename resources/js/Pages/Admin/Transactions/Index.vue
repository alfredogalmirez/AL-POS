<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    transactions: Object, // Paginated data from Laravel
    stats: Object // Total sales, count, etc.
});

// Format currency helper
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(value);
};
</script>

<template>

    <Head title="Transactions Report" />

    <AdminLayout>
        <div class="space-y-6 max-w-7xl mx-auto p-6">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-800">Transaction History</h1>
                    <p class="text-slate-500 font-medium">Monitor and export your POS sales data.</p>
                </div>

                <a :href="route('admin.transactions.export')"
                    class="flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-emerald-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export to CSV
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-6 bg-white rounded-[2rem] border border-slate-100 shadow-sm">
                    <p class="text-slate-500 text-sm font-bold uppercase tracking-wider">Total Sales (Today)</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">{{ formatCurrency(stats?.today_total || 0) }}
                    </h3>
                </div>
                <div class="p-6 bg-white rounded-[2rem] border border-slate-100 shadow-sm">
                    <p class="text-slate-500 text-sm font-bold uppercase tracking-wider">Transaction Count</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">{{ stats?.count || 0 }}</h3>
                </div>
                <div class="p-6 bg-white rounded-[2rem] border border-slate-100 shadow-sm">
                    <p class="text-slate-500 text-sm font-bold uppercase tracking-wider">Avg. Basket Size</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">{{ formatCurrency(stats?.avg || 0) }}</h3>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="p-5 font-bold text-slate-600">Invoice</th>
                            <th class="p-5 font-bold text-slate-600">Date/Time</th>
                            <th class="p-5 font-bold text-slate-600">Cashier</th>
                            <th class="p-5 font-bold text-slate-600">Items</th>
                            <th class="p-5 font-bold text-slate-600 text-right">Total Amount</th>
                            <th class="p-5 font-bold text-slate-600 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-for="order in transactions.data" :key="order.id"
                            class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-5 font-black text-blue-600">#{{ order.id }}</td>
                            <td class="p-5 text-slate-500 font-medium">{{ order.formatted_date }}</td>
                            <td class="p-5 text-slate-700 font-bold">{{ order.cashier?.name ?? 'System' }}</td>
                            <td class="p-5 text-slate-500">
                                <div class="flex flex-col gap-1">
                                    <div>
                                        <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded-md text-[10px] font-bold">
                                            {{ order.items_count }} Items
                                        </span>
                                    </div>

                                    <div class="text-sm text-slate-600 font-medium truncate max-w-[250px]"
                                        :title="order.items.map(i => i.product?.name).join(', ')">
                                        {{order.items.map(item => item.product?.name).join(', ')}}
                                    </div>

                                    <span v-if="order.items.length > 2" class="text-[10px] text-slate-400 italic">
                                        + more items
                                    </span>
                                </div>
                            </td>
                            <td class="p-5 text-right font-black text-slate-800">
                                {{ formatCurrency(order.total) }}
                            </td>
                            <td class="p-5 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase"
                                    :class="order.status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                                    {{ order.status }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="p-6 border-t border-slate-100 flex justify-between items-center">
                    <p class="text-sm text-slate-500 font-medium">Showing page {{ transactions.current_page }} of {{
                        transactions.last_page }}</p>
                    <div class="flex gap-2">
                        <template v-for="link in transactions.links" :key="link.label">
                            <Link v-if="link.url" :href="link.url" v-html="link.label"
                                class="px-4 py-2 rounded-xl text-sm font-bold transition-all"
                                :class="link.active ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" />

                            <span v-else v-html="link.label"
                                class="px-4 py-2 roundex-xl text-sm font-bold text-slate-200 bg-slate-50">
                            </span>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
