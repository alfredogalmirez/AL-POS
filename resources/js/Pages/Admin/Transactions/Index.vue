<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { jsPDF } from "jspdf";
import autoTable from 'jspdf-autotable';
import { reactive, ref, watch } from 'vue';

const props = defineProps({
    transactions: Object, // Paginated data from Laravel
    stats: Object, // Total sales, count, etc.
    filters: Object
});

const filterForm = reactive({
    date: props.filters.date || '',
    month: props.filters.month || '',
    year: props.filters.year || '',
})

const isPreviewOpen = ref(false);
const pdfUrl = ref(null);

watch(filterForm, () => {
    router.get(route('admin.transactions.index'), filterForm, {
        preserveState: true,
        replace: true,
        preserveScroll: true,
    });
}, { deep: true });

const resetFilters = () => {
    filterForm.date = '',
        filterForm.month = '',
        filterForm.year = ''
};

// Format currency helper
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(value);
};

const handlePreview = () => {
    try {
        const doc = new jsPDF();

        doc.setFontSize(18);
        doc.text("Transaction History Report", 14, 22);

        doc.setFontSize(11);
        doc.setTextColor(100);
        doc.text(`Generated on: ${new Date().toLocaleDateString()}`, 14, 30);

        autoTable(doc, {
            startY: 35,
            head: [['Date', 'Invoice', 'Cashier', 'Item Details', 'Total', 'Payment']],
            body: props.transactions.data.map(transaction => [
                new Date(transaction.created_at).toLocaleDateString(),
                transaction.invoice_id,
                transaction.cashier?.name || 'System',

                transaction.items.map(item => {

                    return `${item.product?.name} (x${item.quantity}) @ P${item.price}`
                }).join('\n'),

                `P${transaction.total}`,
                (transaction.payment_method || 'N/A').toUpperCase(),
            ]),
            styles: { overflow: 'linebreak', fontSize: 9, cellPadding: 3 },
            columnStyles: { 3: { cellWidth: 'auto' }, 4: { halign: 'right' } },
            headStyles: { fillColor: [30, 41, 59], textColor: [255, 255, 255], fontStyle: 'bold' }
        });

        const blob = doc.output('blob');

        pdfUrl.value = URL.createObjectURL(blob);

        isPreviewOpen.value = true;
    } catch (error) {
        console.log("PDF Generation Error:", error);
        alert("Could not generate PDF. Check the browser console for details.");
    }
}

const closePreview = () => {
    isPreviewOpen.value = false;
    if (pdfUrl.value) {
        URL.revokeObjectURL(pdfUrl.value);
        pdfUrl.value = null;
    };
}
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

                <div class="flex  items-center gap-8">
                    <button @click="handlePreview"
                        class="px-6 py-3 bg-red-600 text-white rounded-2xl font-bold hover:bg-red-700 transition-colors shadow-lg shadow-red-100 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg> Preview Report
                    </button>

                    <a :href="route('admin.transactions.export')"
                        class="flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-emerald-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export to CSV
                    </a>
                </div>
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

            <div
                class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm mb-6 items-end">

                <div class="space-y-1.5">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Specific
                        Date</label>
                    <input type="date" v-model="filterForm.date"
                        class="w-full bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all" />
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Month</label>
                    <select v-model="filterForm.month"
                        class="w-full bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all">
                        <option value="">All Months</option>
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Year</label>
                    <input type="number" v-model="filterForm.year" placeholder="2026"
                        class="w-full bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all" />
                </div>

                <div class="flex gap-2">
                    <button @click="resetFilters"
                        class="flex-1 py-3 bg-slate-100 text-slate-500 rounded-2xl font-bold hover:bg-slate-200 transition-all text-sm">
                        Reset
                    </button>
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
                        <template v-if="transactions.data.length > 0">
                            <tr v-for="order in transactions.data" :key="order.id"
                                class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-5 font-black text-blue-600">#{{ order.invoice_id }}</td>
                                <td class="p-5 text-slate-500 font-medium">{{ order.formatted_date }}</td>
                                <td class="p-5 text-slate-700 font-bold">{{ order.cashier?.name ?? 'System' }}</td>
                                <td class="p-5 text-slate-500">
                                    <div class="flex flex-col gap-1">
                                        <div>
                                            <span
                                                class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded-md text-[10px] font-bold">
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
                        </template>

                        <tr v-else>
                            <td colspan="6" class="p-20">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <div
                                        class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-slate-700">No Transactions Found</h3>
                                    <p class="text-slate-500 max-w-xs mx-auto mt-1">
                                        We couldn't find any records matching your current filters. Try adjusting the
                                        date or month.
                                    </p>

                                    <button @click="resetFilters"
                                        class="mt-6 text-blue-600 font-black text-sm hover:underline">
                                        Clear all filters
                                    </button>
                                </div>
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

    <div v-if="isPreviewOpen"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-10 bg-slate-900/60 backdrop-blur-md">

        <div
            class="bg-white w-full max-w-6xl h-full rounded-[2.5rem] shadow-2xl flex flex-col overflow-hidden border border-white/20">

            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h3 class="text-xl font-black text-slate-800">Report Preview</h3>
                    <p class="text-sm text-slate-500 font-medium">Review the transaction history before printing or
                        saving.
                    </p>
                </div>

                <button @click="closePreview"
                    class="w-10 h-10 flex items-center justify-center bg-white rounded-full shadow-sm text-slate-400 hover:text-red-500 transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex-1 bg-slate-100 p-4">
                <iframe :src="pdfUrl" class="w-full h-full rounded-2xl border-none shadow-inner"></iframe>
            </div>

            <div class="p-6 border-t border-slate-100 flex justify-end gap-3 bg-white">
                <button @click="closePreview" class="px-6 py-2 text-slate-500 font-bold hover:bg-slate-50 rounded-xl">
                    Close Preview
                </button>
                <a :href="pdfUrl" download="Transaction_Report.pdf"
                    class="px-6 py-2 bg-blue-600 text-white font-black rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-100">
                    Download PDF
                </a>
            </div>
        </div>
    </div>
</template>
