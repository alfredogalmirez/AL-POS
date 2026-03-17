<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const imagePreview = ref(null);

const form = useForm({
    name: '',
    price: '',
    stock: 0,
    description: '',
    image: null,
});

// Handle image selection and preview
const handleImageChange = (e) => {
    const file = e.target.files[0];
    form.image = file;
    if (file) {
        imagePreview.value = URL.createObjectURL(file);

    }
};

const submit = () => {
    form.post(route('admin.products.store'), {
        forceFormData: true, // Necessary for file uploads
        onSuccess: () => form.reset(),
    });
};
</script>

<template>

    <Head title="Add New Product | AL-POS" />

    <AdminLayout>
        <div class="p-8 bg-slate-50 min-h-full">
            <div class="max-w-2xl mx-auto">

                <div class="mb-10">
                    <Link :href="route('admin.products.index')"
                        class="group inline-flex items-center text-slate-400 hover:text-blue-600 font-bold text-sm transition-colors">
                        <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Inventory
                    </Link>
                    <h1 class="text-4xl font-black text-slate-900 mt-4 tracking-tighter">Add New Product</h1>
                </div>

                <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100">
                    <form @submit.prevent="submit" class="space-y-8">

                        <div>
                            <label class="block text-sm font-black text-slate-700 uppercase tracking-wider mb-4">Product
                                Image</label>
                            <div class="flex items-center gap-6">
                                <div
                                    class="relative w-32 h-32 max-h-full rounded-[2rem] bg-slate-50 border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                                    <img v-if="imagePreview" :src="imagePreview"
                                        class="absolute inset-0 w-full h-full object-cover z-10" />

                                    <svg v-else class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>

                                <div class="flex-1">
                                    <input type="file" @input="handleImageChange" accept="image/*" class="w-full text-sm text-slate-500
                                        file:mr-4 file:py-3 file:px-6
                                        file:rounded-2xl file:border-0
                                        file:text-sm file:font-black
                                        file:bg-blue-50 file:text-blue-600
                                        hover:file:bg-blue-100 transition-all cursor-pointer" />
                                    <p class="mt-2 text-xs text-slate-400 font-medium">PNG, JPG or WebP. Max 2MB.</p>
                                </div>
                            </div>
                            <p v-if="form.errors.image" class="mt-2 text-sm text-red-500 font-bold">{{ form.errors.image
                            }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-black text-slate-700 uppercase tracking-wider mb-2">Product
                                Name</label>
                            <input v-model="form.name" type="text"
                                class="w-full px-6 py-4 rounded-2xl border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-medium"
                                placeholder="e.g. Beef Biryani" required>
                            <p v-if="form.errors.name" class="mt-2 text-sm text-red-500 font-bold">{{ form.errors.name
                            }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <label
                                    class="block text-sm font-black text-slate-700 uppercase tracking-wider mb-2">Price
                                    (₱)</label>
                                <input v-model="form.price" type="number" step="0.01"
                                    class="w-full px-6 py-4 rounded-2xl border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-medium"
                                    placeholder="0.00" required>
                                <p v-if="form.errors.price" class="mt-2 text-sm text-red-500 font-bold">{{
                                    form.errors.price }}</p>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-black text-slate-700 uppercase tracking-wider mb-2">Initial
                                    Stock</label>
                                <input v-model="form.stock" type="number"
                                    class="w-full px-6 py-4 rounded-2xl border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-medium"
                                    required>
                                <p v-if="form.errors.stock" class="mt-2 text-sm text-red-500 font-bold">{{
                                    form.errors.stock }}</p>
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-black text-slate-700 uppercase tracking-wider mb-2">Description</label>
                            <textarea v-model="form.description" rows="4"
                                class="w-full px-6 py-4 rounded-2xl border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all outline-none font-medium resize-none"
                                placeholder="Tell us about this product..."></textarea>
                            <p v-if="form.errors.description" class="mt-2 text-sm text-red-500 font-bold">{{
                                form.errors.description }}</p>
                        </div>

                        <div class="pt-4">
                            <button type="submit" :disabled="form.processing"
                                class="w-full bg-blue-600 text-white py-5 rounded-[1.5rem] font-black text-xl hover:bg-blue-700 transition-all shadow-xl shadow-blue-100 disabled:opacity-50 flex items-center justify-center gap-3">
                                <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                {{ form.processing ? 'Saving Product...' : 'Create Product' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
