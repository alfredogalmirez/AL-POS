<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    username: '',
    password: '',
    password_confirmation: '',
    role: 'cashier',
});

const submit = () => {
    form.post(route('admin.cashier.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}

</script>
<template>

    <Head title="Add Cashier" />

    <AdminLayout>
        <div class="min-h-screen bg-slate-50/50 py-12 px-4">
            <div class="max-w-3xl mx-auto">

                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-black text-slate-800 tracking-tight">Onboard Staff</h1>
                        <p class="text-slate-500 font-medium">Create a new cashier account for your POS system.</p>
                    </div>
                    <div
                        class="h-12 w-12 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                    <form @submit.prevent="submit" class="p-10 space-y-8">

                        <div class="grid grid-cols-1 gap-6">
                            <div class="space-y-2">
                                <label class="text-[11px] font-black uppercase tracking-widest text-slate-700 ml-1">Full
                                    Name</label>
                                <input v-model="form.name" type="text" placeholder="e.g. Juan Dela Cruz"
                                    class="w-full bg-slate-50 border-none rounded-2xl p-4 font-medium text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all"
                                    :class="{ 'ring-2 ring-red-500': form.errors.name }">
                                <p v-if="form.errors.name" class="text-xs font-bold text-red-500 ml-1">{{
                                    form.errors.name }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label
                                    class="text-[11px] font-black uppercase tracking-widest text-slate-300 ml-1">Username</label>

                                <div
                                    class="flex items-stretch bg-slate-50 rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 transition-all">
                                    <div
                                        class="flex items-center justify-center px-5 bg-slate-100 border-r border-slate-200 select-none">
                                        <span class="text-xs font-black text-slate-500 tracking-tight">
                                            CSR-
                                        </span>
                                    </div>

                                    <input v-model="form.username" type="text" placeholder="Username"
                                        class="flex-1 bg-transparent border-none p-4 font-medium text-slate-700 placeholder:text-slate-300 focus:ring-0"
                                        :class="{ 'ring-2 ring-red-500': form.errors.username }">
                                </div>
                                <p v-if="form.errors.username" class="text-xs font-bold text-red-500 ml-1">{{
                                    form.errors.username }}</p>
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="text-[11px] font-black uppercase tracking-widest text-slate-700 ml-1">System
                                    Role</label>
                                <div class="relative">
                                    <input v-model="form.role" type="text" readonly
                                        class="w-full bg-slate-100 border-none rounded-2xl p-4 font-medium text-slate-300 cursor-not-allowed">
                                    <span
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] bg-white px-2 py-1 rounded-lg shadow-sm font-black text-slate-500 uppercase">Locked</span>
                                </div>
                            </div>
                        </div>

                        <div class="h-px bg-slate-50 w-full"></div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label
                                    class="text-[11px] font-black uppercase tracking-widest text-slate-700 ml-1">Password</label>
                                <input v-model="form.password" type="password" placeholder="••••••••"
                                    class="w-full bg-slate-50 border-none rounded-2xl p-4 font-medium text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all placeholder:text-slate-300"
                                    :class="{ 'ring-2 ring-red-500': form.errors.password }">
                                <p v-if="form.errors.password" class="text-xs font-bold text-red-500 ml-1">{{
                                    form.errors.password }}</p>
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="text-[11px] font-black uppercase tracking-widest text-slate-700 ml-1">Repeat
                                    Password</label>
                                <input v-model="form.password_confirmation" type="password" placeholder="••••••••"
                                    class="w-full bg-slate-50 border-none rounded-2xl p-4 font-medium text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all placeholder:text-slate-300">
                            </div>
                        </div>

                        <div class="pt-6 flex items-center justify-between">
                            <button type="button" @click="form.reset()"
                                class="text-sm font-black text-slate-700 hover:text-red-500 transition-colors uppercase tracking-widest">
                                Clear Form
                            </button>

                            <button type="submit" :disabled="form.processing"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-4 rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl shadow-blue-100 active:scale-95 transition-all disabled:opacity-50">
                                <span v-if="form.processing" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Processing...
                                </span>
                                <span v-else>Register Cashier</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>