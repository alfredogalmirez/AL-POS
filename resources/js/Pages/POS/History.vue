<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    transactions: Object, // Laravel Paginated object
});

// Access auth user from usePage (Inertia's global data)
const user = computed(() => usePage().props.auth.user);

// Helper for currency
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(value);
};

// Calculate today's sales from the current page collection
const pageTotal = computed(() => {
    return props.transactions.data.reduce((acc, order) => acc + parseFloat(order.total), 0);
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </Link>
                        <h1 class="text-3xl font-black text-slate-800">Recent Transactions</h1>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">Cashier Session</p>
                        <p class="font-bold text-slate-700">{{ user?.name || 'Guest' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 space-y-4">
                        <div v-for="order in transactions.data" :key="order.id"
                            class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 hover:border-blue-500 transition-all group">

                            <div class="flex flex-col md:flex-row justify-between gap-6">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-4">
                                        <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-xs font-black uppercase tracking-tighter">
                                            #{{ order.id }}
                                        </span>
                                        <p class="text-slate-400 font-bold text-sm">
                                            {{ new Date(order.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}
                                        </p>
                                    </div>

                                    <div class="space-y-3">
                                        <div v-for="item in order.items" :key="item.id" class="flex items-center justify-between group/item">
                                            <div class="flex items-center gap-3">
                                                <span class="w-6 h-6 flex items-center justify-center bg-slate-50 text-slate-400 text-[10px] font-black rounded-lg">
                                                    {{ item.quantity }}x
                                                </span>
                                                <p class="text-slate-700 font-bold text-sm">
                                                    {{ item.product?.name || 'Product Deleted' }}
                                                </p>
                                            </div>
                                            <p class="text-slate-400 font-medium text-sm">
                                                {{ formatCurrency(item.price) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col justify-between items-end border-t md:border-t-0 md:border-l border-slate-50 pt-6 md:pt-0 md:pl-8">
                                    <div class="text-right">
                                        <p class="text-xs font-black text-slate-300 uppercase tracking-widest mb-1">Grand Total</p>
                                        <p class="text-3xl font-black text-slate-900 leading-none">
                                            {{ formatCurrency(order.total) }}
                                        </p>
                                    </div>
                                    <div class="mt-4 flex gap-2">
                                        <span class="px-4 py-2 bg-green-50 text-green-600 rounded-xl text-xs font-black uppercase tracking-widest">
                                            Paid
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="transactions.data.length === 0" class="bg-white p-20 rounded-[3rem] text-center border-2 border-dashed border-slate-100">
                            <p class="text-slate-400 font-bold italic">No transactions found for this session.</p>
                        </div>

                        <div class="mt-8 flex gap-2">
                            <Link v-for="link in transactions.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                v-html="link.label"
                                class="px-4 py-2 rounded-lg text-sm border transition-all"
                                :class="{ 'bg-blue-600 text-white border-blue-600': link.active, 'bg-white text-slate-600 border-slate-100': !link.active, 'opacity-50 pointer-events-none': !link.url }"
                            />
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
</template>
