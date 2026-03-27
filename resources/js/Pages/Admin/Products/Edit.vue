<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Toast from '@/Components/Toast.vue';

const props = defineProps({
    product: Object,
    categories: Array,
});

const form = useForm({
    _method: 'put',
    name: props.product.name,
    price: props.product.price,
    stock: props.product.stock,
    description: props.product.description,
    image: null,
    category_id: props.product.category_id,
});

const imagePreview = ref(props.product.image ? `/storage/${props.product.image}` : null);

const handleImageChange = (e) => {
    const file = e.target.files[0];
    form.image = file;

    imagePreview.value = URL.createObjectURL(file);
};

const submit = () => {
    form.post(route('admin.products.update', props.product.id), {
        preserveScroll: true,
    });
};

</script>

<template>

    <Head title="Edit Product" />

    <AdminLayout>
        <div class="max-w-4xl mx-auto">
            <div class="mb-8 flex justify-between items-end">
                <div>
                    <Link :href="route('admin.products.index')"
                        class="group inline-flex items-center text-slate-400 hover:text-blue-600 font-bold text-sm transition-colors">
                        <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Inventory
                    </Link>
                    <h1 class="text-3xl font-black text-slate-800 mt-4">Edit Product</h1>
                    <p class="text-slate-400 font-medium">Update the details for <span class="text-blue-600">#{{
                            product.id }}</span></p>
                </div>
            </div>

            <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100">
                <form @submit.prevent="submit" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">

                        <div class="space-y-4">
                            <label class="block text-sm font-black text-slate-700 uppercase tracking-widest">Product
                                Image</label>

                            <div class="relative group">
                                <div
                                    class="aspect-square rounded-[2rem] bg-slate-50 overflow-hidden border-2 border-dashed border-slate-200 flex items-center justify-center">
                                    <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />

                                    <div v-else class="text-center">
                                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-xs font-bold text-slate-400 uppercase">No Image Set</span>
                                    </div>
                                </div>
                            </div>

                            <input type="file" @change="handleImageChange"
                                class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-600 file:font-black cursor-pointer hover:file:bg-blue-100 transition-all">
                            <p class="mt-2 text-xs text-slate-400 font-medium">PNG, JPG or WebP. Max 2MB.</p>
                            <p v-if="form.errors.image" class="text-red-500 text-xs font-bold mt-1">{{ form.errors.image
                                }}</p>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label
                                    class="block text-sm font-black text-slate-700 uppercase tracking-wider mb-2">Product
                                    Name</label>
                                <input type="text" v-model="form.name"
                                    class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-medium"
                                    placeholder="Enter product name">
                                <p v-if="form.errors.name" class="text-red-500 text-xs font-bold mt-1">{{
                                    form.errors.name }}</p>
                            </div>

                            <div class="space-y-2 mb-4">
                                <label class="text-sm font-bold text-slate-700">Category</label>
                                <div>
                                    <select v-model="form.category_id"
                                        class="w-full p-4 rounded-2xl border-none bg-slate-100 focus:ring-2 focus:ring-blue-500 font-bold text-slate-700 appearance-none">
                                        <option value="" disabled>Select a category</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </div>
                                <div v-if="form.errors.category_id" class="text-red-500 text-xs font-bold mt-1">
                                    {{ form.errors.category_id }}
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-black text-slate-700 uppercase tracking-wider mb-2">Price
                                        (₱)</label>
                                    <input type="number" step="0.01" v-model="form.price"
                                        class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-medium">
                                    <p v-if="form.errors.price" class="text-red-500 text-xs font-bold mt-1">{{
                                        form.errors.price }}</p>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-black text-slate-700 uppercase tracking-wider mb-2">Stock</label>
                                    <input type="number" v-model="form.stock"
                                        class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-medium">
                                    <p v-if="form.errors.stock" class="text-red-500 text-xs font-bold mt-1">{{
                                        form.errors.stock }}</p>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-black text-slate-700 uppercase tracking-wider mb-2">Description</label>
                                <textarea v-model="form.description" rows="4"
                                    class="w-full px-6 py-4 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-medium resize-none"
                                    placeholder="Product description..."></textarea>
                                <p v-if="form.errors.description" class="text-red-500 text-xs font-bold mt-1">{{
                                    form.errors.description }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-slate-50 flex items-center justify-between">
                        <p class="text-sm text-slate-400 font-medium italic">All changes are immediate upon saving.</p>
                        <button type="submit" :disabled="form.processing"
                            class="px-12 bg-blue-600 text-white py-5 rounded-[1.5rem] font-black text-xl hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ form.processing ? 'Updating...' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
