<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    products: Array,
    total: [Number, String],
    categories: Array,
    filters: Object,
    cart: Object,
    print_data: Array
});

const search = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category ? Number(props.filters.category) : null)

const setCategory = (id) => {
    selectedCategory.value = id;

    router.get(route('pos.index'), {
        category: id,
        search: search.value,
    }, {
        preserveState: true,
        replace: true,
        only: ['products'],
    });
}

watch(search, debounce((value) => {
    router.get(route('pos.index'), {
        search: value,
        category: selectedCategory.value,
    },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true,
            only: ['products'],
        });
}, 300));

// Helper for currency formatting
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
    }).format(value);
};

const page = usePage();

const cartArray = computed(() => Object.values(props.cart || {}));

const cart = computed(() => page.props.cart || {});

// Handle Add to Cart via Inertia
const addToCart = (product) => {
    if (product.stock <= 0) {
        alert("Cannot add to cart: Out of Stock!");
        return;
    }

    const existingItem = cartArray.value.find(i => i.id === product.id);

    if (existingItem && existingItem.quantity >= product.stock) {
        alert("Maximum stock reached!");
        return;
    }

    router.post(route('pos.addToCart', product), {}, {
        preserveScroll: true,
    });
};

// Handle Remove via Inertia
const removeFromCart = (id) => {
    router.delete(route('pos.remove', id), {
        preserveScroll: true,
    });
};

const form = useForm({
    payment_method: 'cash',
    amount_received: 0,
});

const handleCheckout = () => {
    if(form.payment_method === 'cash'){
         form.post(route('pos.checkout'), {
        preventScroll: true,
        onSuccess: () => form.reset(),
    });
    } else {
       if(form.payment_method === 'gcash'){
        startPaymongoPayment();
       }
    }
}

const startPaymongoPayment = async () => {
     try {
            const response = await axios.post(route('pos.checkout.paymongo'));

            window.location.href = response.data.checkout_url
        } catch (error) {
            alert('Could not connect to PayMongo. Please try again.');
        }
}

const updateQuantity = (id, amount) => {
    router.post(route('pos.updateQuantity'), {
        product_id: id,
        change: amount,
    }, {
        preventScroll: true,
        onSuccess: () => {

        }
    });
}

const handleManualInput = (id, event) => {
    const newValue = parseInt(event.target.value);

    if (newValue <= 0) {
        removeFromCart(id);
        return;
    }

    router.post(route('pos.setQuantity'), {
        product_id: id,
        quantity: newValue,
    }, {
        preserveScroll: true,
    });
}

// // Auto-print logic if print_data exists
// onMounted(() => {
//     if (props.print_data) {
//         setTimeout(() => {
//             window.print();
//         }, 1000); // Shorter delay for Vue
//     }
// });
</script>

<template>

    <Head title="POS Menu" />

    <AuthenticatedLayout>
        <div class="grid grid-cols-1 lg:grid-cols-3 h-screen max-w-full bg-gray-100 overflow-hidden">

            <div class="col-span-2 p-6 overflow-y-auto h-full">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div class="flex-shrink-0">
                        <h1 class="text-2xl font-bold">Menu Order</h1>
                    </div>

                    <div class="relative w-full md:w-72 lg:w-96">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input v-model="search" type="text" placeholder="Search products..."
                            class="w-full pl-12 pr-4 py-4 rounded-2xl bg-wite border-transparent focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none shadow-sm font-medium text-slate-600">

                        <button v-if="search" @click="search = ''"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-red-500 transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-3 mb-6 overflow-x-auto no-scrollbar pb-2">
                    <button @click="setCategory(null)" :class="[
                        'px-8 py-3 rounded-2xl font-bold transition-all whitespace-nowrap border-2',
                        !selectedCategory
                            ? 'bg-blue-600 text-white border-blue-600 shadow-lg'
                            : 'bg-white text-slate-500 border-transparent hover:bg-slate-50'
                    ]">
                        All Menu
                    </button>

                    <button v-for="cat in categories" :key="cat.id" @click="setCategory(cat.id)" :class="[
                        'px-8 py-3 rounded-2xl font-bold transition-all whitespace-nowrap border-2',
                        selectedCategory == cat.id
                            ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-100'
                            : 'bg-white text-slate-500 border-transparent hover:bg-slate-50'
                    ]">
                        {{ cat.name }}
                    </button>
                </div>


                <div v-if="products.length > 0" class="grid grid-cols-3 gap-4">
                    <div v-for="product in products" :key="product.id"
                        class="relative bg-white p-4 rounded-[2rem] shadow-sm border border-slate-100 hover:border-blue-500 hover:shadow-md transition-all group">

                        <div class="absolute top-3 left-3 z-10 px-2 py-1 rounded-lg flex items-center gap-1.5 border"
                            :class="product.stock > 0
                                ? 'bg-green-50 text-green-600 border-slate-200'
                                : 'bg-red-50 text-red-600 border-red-100'">

                            <span class="w-1.5 h-1.5 rounded-full"
                                :class="product.stock > 0 ? 'bg-green-500' : 'bg-red-500'"></span>

                            <span class="text-[10px] font-black uppercase tracking-wider">
                                {{ product.stock > 0 ? `${product.stock} IN STOCK` : 'SOLD OUT' }}
                            </span>
                        </div>

                        <div
                            class="aspect-square w-full mb-4 overflow-hidden rounded-[1.5rem] bg-slate-50 border border-slate-50">
                            <img v-if="product.image" :src="`/storage/${product.image}`"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            <div v-else class="w-full h-full bg-slate-100 flex items-center justify-center">
                                <span class="text-slate-400 font-black text-xl uppercase">
                                    {{ product.name.charAt(0) }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-1 mb-4 px-1">
                            <h3 class="font-black text-slate-800 leading-tight truncate">{{ product.name }}</h3>
                            <p class="text-blue-600 font-black text-lg">{{ formatCurrency(product.price) }}</p>
                        </div>

                        <button @click="addToCart(product)" :disabled="product.stock <= 0" :class="['w-full py-3 rounded-2xl text-sm font-bold active:scale-95 transition-all flex items-center justify-center gap-2',
                            product.stock > 0 ? 'bg-slate-900 text-white active:scale-95' : 'bg-slate-100 text-slate-400 cursor-not-allowed'
                        ]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            {{ product.stock > 0 ? 'Add to Cart' : 'Out of Stock' }} </button>
                    </div>
                </div>

                <div v-else
                    class="col-span-full py-20 flex flex-col items-center justify-center bg-slate-50 border-2 border-dashed border-slate-200 rounded-[3rem]">
                    <div class="opacity-30 mb-4">
                        <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-400">No products found</h3>
                </div>
            </div>

            <div class="col-span-1 bg-white border-l flex flex-col h-screen shadow-2xl">
                <div class="p-6 border-b">
                    <h2 class="text-2xl font-black text-slate-800">Current Order</h2>
                    <p class="text-sm text-slate-400">Order #{{ new Date().toLocaleDateString() }}</p>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-6 no-scrollbar">
                    <div v-if="cart && Object.keys(cart).length > 0" class="space-y-6">
                        <div v-for="(details, id) in cart" :key="id"
                            class="flex items-center bg-white p-4 rounded-3xl border border-slate-100 hover:border-blue-100 transition-all group">

                            <div class="flex items-center gap-4 flex-1 min-w-0">
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="font-black text-slate-800 leading-tight uppercase text-sm tracking-tight truncate">
                                        {{ details.name }}</p>

                                    <p class="text-xs text-slate-400">{{ formatCurrency(details.price) }}</p>
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-center bg-slate-100 rounded-2xl p-1 border border-slate-100 mx-4 flex-shrink-0 w-[140px]">
                                <button @click="updateQuantity(id, -1)"
                                    class="w-8 h-8 flex items-center justify-center bg-white rounded-lg shadow-sm hover:bg-red-50 hover:text-red-600 transition-colors">
                                    <span class="font-bold leading-none">-</span>
                                </button>

                                <input type="number" :value="details.quantity" @change="handleManualInput(id, $event)"
                                    class="w-14 text-center bg-transparent border-none focus:ring-0 font-black text-slate-700 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">

                                <button @click="updateQuantity(id, 1)"
                                    class="w-8 h-8 flex items-center justify-center bg-white rounded-lg shadow-sm hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                    <span class="font-bold">+</span>
                                </button>
                            </div>


                            <div class="flex items-center gap-4 justify-end w-[110px] flex-shrink-0">
                                <span class="font-bold text-slate-700 text-sm whitespace-nowrap">{{
                                    formatCurrency(details.price *
                                        details.quantity) }}</span>
                                <button @click="removeFromCart(id)"
                                    class="text-slate-300 hover:text-red-500 transition-colors">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-else
                        class="flex flex-col items-center justify-center h-full text-center space-y-4 opacity-40">
                        <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                d="M16 11V7a4 4 0 118 0m-9 5a4 4 0 00-8 0v4a2 2 0 002 2h10a2 2 0 002-2v-4a2 2 0 00-2-2z" />
                        </svg>
                        <p class="text-lg font-medium">Cart is empty</p>
                    </div>
                </div>

                <div class="p-6 bg-slate-50 border-t space-y-4">
                    <div class="space-y-2 border-b pb-4">
                        <div class="flex justify-between text-slate-500 text-sm">
                            <span>Subtotal</span>
                            <span>{{ formatCurrency(total) }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-xl font-black text-slate-800">Total</span>
                            <span class="text-2xl font-black text-blue-600">{{ formatCurrency(total) }}</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <p class="text-lg font-black text-slate-800 flex items-center">Payment Method</p>
                        <label class="cursor-pointer">
                            <input type="radio" v-model="form.payment_method" value="cash" class="peer hidden">
                            <div
                                class="text-center py-2 rounded-lg border-2 border-slate-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 text-xs font-bold transition-all">
                                CASH
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" v-model="form.payment_method" value="gcash" class="peer hidden">
                            <div
                                class="text-center py-2 rounded-lg border-2 border-slate-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 text-xs font-bold transition-all">
                                GCASH
                            </div>
                        </label>
                    </div>

                    <div v-if="form.payment_method === 'cash'" class="flex gap-4">
                        <p class="text-lg font-black text-slate-800 flex items-center">Amount Received</p>
                        <input v-model="form.amount_received" type="number" placeholder="Input amount received..."
                            class="rounded-lg border-1 flex-1 focus:ring-2 focus:ring-blue-500 transition-all outline-none">
                    </div>

                    <div class="flex justify-center items-center">
                        <span v-if="form.amount_received > total && Object.keys(cart).length > 0"
                            class="text-lg font-black text-slate-800">Change: {{ formatCurrency(form.amount_received -
                                total) }} </span>
                    </div>

                    <button @click="handleCheckout"
                        :disabled="form.processing || total <= 0 && cart.length === 0 || form.payment_method === 'cash' && form.amount_received < total"
                        :class="[
                            'w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-bold text-lg shadow-lg shadow-blue-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed',
                            { 'active:scale-95': !form.processing && total > 0 }
                        ]">
                        {{ form.processing ? 'Processing...' : 'Pay ' + formatCurrency(total) }}
                    </button>
                </div>
            </div>
        </div>


        <!-- FOR PRINTING LATER -->
        <!-- <div class="hidden print:block receipt-container mx-auto text-base font-mono text-black p-2 w-[80mm]">
            <div class="text-center mb-6">
                <h2 class="font-bold text-2xl uppercase">AL POS SYSTEM</h2>
                <p>San Pedro, Laguna</p>
                <p>{{ new Date().toLocaleString() }}</p>
            </div>
            <div class="border-b border-dashed border-black mb-2"></div>
            <table class="w-full mb-2 text-sm">
                <tbody>
                    <tr v-for="item in print_data" :key="item.name">
                        <td class="uppercase">{{ item.name }}</td>
                        <td class="text-right">{{ item.quantity }}</td>
                        <td class="text-right">{{ formatCurrency(item.price * item.quantity) }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="border-t border-dashed border-black pt-2 font-black">
                <div class="flex justify-between text-xl">
                    <span>TOTAL:</span>
                    <span>{{ formatCurrency(total) }}</span>
                </div>
            </div>
        </div> -->
    </AuthenticatedLayout>
</template>
