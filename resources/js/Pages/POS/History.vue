<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { jsPDF } from 'jspdf';
import { computed, ref } from 'vue';
import autoTable from 'jspdf-autotable';

const props = defineProps({
    transactions: Object, // Laravel Paginated object
});

const voidOrder = (id) => {
    if (confirm('Are you sure you want to void this order? Stock will be returned.')) {
        router.post(route('pos.transaction.void', id));
    }
}

const isPreviewOpen = ref(false);
const pdfUrl = ref(null);

const handlePreview = (order) => {
    const doc = new jsPDF({
        unit: 'mm',
        format: [80, 150] // Typical thermal receipt size (80mm width)
    });

    doc.setFont("Helvetica");

    const P = "P";
    const peso = "\u20B1";

    // Receipt Header
    doc.setFontSize(12);
    doc.text("OFFICIAL RECEIPT", 40, 10, { align: 'center' });
    doc.setFontSize(8);
    doc.text(`Invoice: ${order.invoice_id}`, 10, 20);
    doc.text(`Date: ${new Date(order.created_at).toLocaleString()}`, 10, 25);
    doc.text(`Cashier: ${usePage().props.auth.user.name}`, 10, 30);

    // Items Table
    autoTable(doc, {
        startY: 35,
        margin: { left: 5, right: 5 },
        head: [['Item', 'Qty', 'Price']],
        body: order.items.map(item => [
            item.product?.name || 'Deleted',
            item.quantity,
            `P ${parseFloat(item.price).toFixed(2)}`
        ]),
        theme: 'plain',
        styles: { fontSize: 7 },
        columnStyles: { 2: { halign: 'right' } }
    });

    // Totals
    const finalY = doc.lastAutoTable.finalY + 10;
    doc.setFontSize(10);
    doc.text(`TOTAL: P ${parseFloat(order.total).toFixed(2)}`, 70, finalY, { align: 'right' });

    // Generate Blob and Open Modal
    const blob = doc.output('blob');
    pdfUrl.value = URL.createObjectURL(blob);
    isPreviewOpen.value = true;
}

const closePreview = () => {
    isPreviewOpen.value = false;
    URL.revokeObjectURL(pdfUrl.value);
};

const searchQuery = ref('');

const filteredList = computed(() => {
    const list = props.transactions.data;

    if (!searchQuery.value) return list;

    const term = searchQuery.value.toLowerCase();

    return list.filter(item => {
        return (
            item.invoice_id.toLowerCase().includes(term) ||
            item.total.toString().includes(term)
        );
    });
});

// Access auth user from usePage (Inertia's global data)
const user = computed(() => usePage().props.auth.user);

// Helper for currency
const formatCurrency = (value) => {
    const amount = parseFloat(value) || 0;

    // \u20B1 is the Javascript Unicode for the Peso Sign ₱
    const pesoSign = "\u20B1";

    return pesoSign + ' ' + amount.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

// Calculate today's sales from the current page collection
const pageTotal = computed(() => {
    return filteredList.value
        .filter(order => order.status === 'completed')
        .reduce((acc, order) => acc + parseFloat(order.total), 0);
});
</script>

<template>

    <Head title="Transaction History" />

    <AuthenticatedLayout>
        <div class="relative z-10 p-6 bg-slate-50 min-h-full">
            <div class="max-w-7xl mx-auto">

                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <Link :href="route('pos.index')"
                            class="p-3 bg-white rounded-2xl shadow-sm border border-slate-100 hover:bg-slate-50 transition-all">
                            <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </Link>
                        <h1 class="text-3xl font-black text-slate-800">Recent Transactions</h1>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">Cashier Session</p>
                        <p class="font-bold text-slate-700">{{ user?.name || 'Guest' }}</p>
                    </div>
                </div>

                <div class="mb-6">
                    <input v-model="searchQuery" type="text" placeholder="Quick search by Invoice ID or Amount..."
                        class="w-full md:w-96 px-6 py-4 bg-white border-none rounded-2xl shadow-sm focus:ring-2 focus:ring-blue-500 font-bold text-slate-600" />
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-4">
                        <div v-for="order in filteredList" :key="order.id"
                            class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 hover:border-blue-500 transition-all group">

                            <div class="flex flex-col md:flex-row justify-between gap-6">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 mb-4">
                                        <span
                                            class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-xs font-black uppercase tracking-tighter">
                                            #{{ order.id }}
                                        </span>
                                        <p class="text-slate-400 font-bold text-sm">
                                            {{ new Date(order.created_at).toLocaleTimeString([], {
                                                hour: '2-digit',
                                                minute: '2-digit'
                                            }) }}
                                        </p>
                                    </div>

                                    <div class="space-y-3">
                                        <div v-for="item in order.items" :key="item.id"
                                            class="grid grid-cols-[auto_1fr_auto] items-center gap-3 py-2 border-b border-slate-50 last:border-0">

                                            <span
                                                class="text-[10px] font-bold text-slate-400 bg-slate-50 px-1.5 py-0.5 rounded">
                                                {{ item.quantity }}x
                                            </span>
                                            <p class="text-slate-700 font-bold text-sm truncate max-w-[150px] md:">
                                                {{ item.product?.name || 'Product Deleted' }}
                                            </p>


                                            <div class="text-right w-24 shrink-0 font-mono text-xs tracking-tighter">
                                                <p class="text-slate-400 font-medium text-sm">
                                                    {{ formatCurrency(item.price) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex flex-col justify-between items-end border-t md:border-t-0 md:border-l border-slate-50 pt-6 md:pt-0 md:pl-8 w-full md:w-[260px] shrink-0">
                                    <div class="text-right flex flex-col items-end gap-2">
                                        <div class="flex flex-col items-end">
                                            <p class="text-xs font-black text-slate-300 uppercase tracking-widest mb-1">
                                                Grand Total</p>
                                            <p class="text-3xl font-black text-slate-900 leading-none">
                                                {{ formatCurrency(order.total) }}
                                            </p>
                                        </div>

                                        <span :class="{
                                            'bg-emerald-100 text-emerald-700 border-emerald-200': order.status === 'completed',
                                            'bg-rose-100 text-rose-700 border-rose-200': order.status === 'void',
                                            'bg-amber-100 text-amber-700 border-amber-200': order.status === 'pending'
                                        }" class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase border">
                                            {{ order.status }}
                                        </span>
                                    </div>

                                    <div class="mt-auto w-full flex gap-2 pt-4">
                                        <button @click=" handlePreview(order)"
                                            class="flex-1 px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-600 transition-colors">
                                            <i class="fas fa-print mr-2"></i> Receipt
                                        </button>

                                        <button v-if="order.status === 'completed'" @click="voidOrder(order.id)"
                                            class="px-4 py-2 bg-white text-slate-400 border border-slate-200 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-all">
                                            Void
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="filteredList.length === 0"
                            class="bg-white p-20 rounded-[3rem] text-center border-2 border-dashed border-slate-100">
                            <p class="text-slate-400 font-bold italic">No transactions found for this session.</p>
                        </div>

                        <div class="mt-8 flex gap-2">
                            <Link v-for="link in transactions.links" :key="link.label" :href="link.url || '#'"
                                v-html="link.label" class="px-4 py-2 rounded-lg text-sm border transition-all"
                                :class="{ 'bg-blue-600 text-white border-blue-600': link.active, 'bg-white text-slate-600 border-slate-100': !link.active, 'opacity-50 pointer-events-none': !link.url }" />
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-blue-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-blue-100">
                            <p class="text-blue-200 font-bold uppercase text-xs tracking-widest mb-2">Cashier Sales</p>
                            <h2 class="text-4xl font-black italic">{{ formatCurrency(pageTotal) }}</h2>
                            <div class="mt-6 pt-6 border-t border-blue-500/50">
                                <p class="text-sm font-medium text-blue-100">
                                    Total Orders: {{ transactions.total }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <div v-if="isPreviewOpen"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4">
        <div class="bg-white w-full max-w-lg rounded-[2.5rem] overflow-hidden shadow-2xl">
            <div class="p-6 border-b flex justify-between items-center">
                <h3 class="font-black text-slate-800">Receipt Preview</h3>
                <button @click="closePreview" class="text-slate-400 hover:text-red-500 font-bold text-xl">×</button>
            </div>
            <div class="h-[500px] bg-slate-100 p-4">
                <iframe :src="pdfUrl" class="w-full h-full rounded-2xl shadow-inner"></iframe>
            </div>
            <div class="p-6 flex gap-3">
                <button @click="closePreview"
                    class="flex-1 py-3 bg-slate-100 text-slate-500 rounded-2xl font-bold">Close</button>
                <a :href="pdfUrl" download="Receipt.pdf"
                    class="flex-1 py-3 bg-blue-600 text-white text-center rounded-2xl font-bold">Download</a>
            </div>
        </div>
    </div>
</template>
