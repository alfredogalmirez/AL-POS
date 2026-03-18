<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ categories: Array });

const editingId = ref(null);

const form = useForm({
    name: '',
});

const editForm = useForm({
    name: '',
})

const submit = () => {
    form.post(route('admin.categories.store'), {
        onSuccess: () => form.reset(),
    });
};

const startEdit = (category) => {
    editingId.value = category.id;
    editForm.name = category.name;
};

const cancelEdit = () => {
    editingId.value = null;
    editForm.reset();
}

const updateCategory = (id) => {
    editForm.put(route('admin.categories.update', {category: id}), {
        onSuccess: () => editingId.value = null,
    });
}

const deleteCategory = (id) => {
    if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
        router.delete(route('admin.categories.delete', { category: id }), {
            preserveScroll: true,
        });
    }
};

</script>

<template>

    <Head title="Manage Category" />

    <AdminLayout>
        <div class="p-6 max-w-2xl mx-auto bg-white rounded-[2rem] shadow-sm border border-slate-100">
            <h2 class="text-xl font-black mb-6">Manage Categories</h2>

            <form @submit.prevent="submit" class="flex gap-2 mb-8">
                <input v-model="form.name" type="text" placeholder="Category Name (e.g. Drinks)"
                    class="flex-1 p-4 rounded-2xl bg-slate-100 border-none focus:ring-2 focus:ring-blue-500">
                <button :disabled="form.processing" class="bg-blue-600 text-white px-6 rounded-2xl font-bold">
                    Add
                </button>
            </form>

            <div class="space-y-2">
                <div v-for="category in categories" :key="category.id"
                    class="p-4 bg-slate-50 rounded-2xl flex justify-between items-center font-bold text-slate-700">
                    <div v-if="editingId === category.id" class="flex-1 flex gap-2 items-center">
                        <input v-model="editForm.name" type="text" autofocus
                            class="flex-1 p-2 px-4 rounded-xl border-none ring-2 ring-blue-500 bg-white font-bold text-slate-700"
                            @keyup.enter="updateCategory(category.id)" @keydown.esc="cancelEdit">

                        <button @click="updateCategory(category.id)" class="text-blue-600 px-2 text-sm">Save</button>
                        <button @click="cancelEdit" class="text-slate-400 px-2 text-sm">Cancel</button>
                    </div>

                    <template v-else>
                        <span>{{ category.name }}</span>

                        <div class="flex gap-1">
                            <button @click="startEdit(category)"
                                class="p-2.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>

                            <button @click="deleteCategory(category.id)"
                                class="p-2.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
