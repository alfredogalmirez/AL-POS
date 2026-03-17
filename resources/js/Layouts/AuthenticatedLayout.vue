<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import Toast from '@/Components/Toast.vue';

</script>

<template>
    <div class="flex h-screen bg-gray-100 overflow-hidden">
        <Toast />

        <nav class="w-24 bg-white border-r flex flex-col items-center py-6 h-screen sticky top-0">
            <div class="flex flex-col h-full gap-8">
                <div
                    class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>

                <div class="flex flex-col gap-4 flex-1">
                    <Link :href="route('pos.index')" class="p-3 rounded-xl transition-all"
                        :class="route().current('pos.index') ? 'text-blue-600 bg-blue-50' : 'text-gray-400 hover:text-blue-600 hover:bg-blue-50'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </Link>

                    <Link :href="route('pos.history')" class="p-3 rounded-xl transition-all"
                        :class="route().current('pos.history') ? 'text-blue-600 bg-blue-50' : 'text-gray-400 hover:text-blue-600 hover:bg-blue-50'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </Link>
                </div>

                <div class="pb-4">
                    <Link :href="route('logout')" method="post" as="button"
                        class="p-3 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </Link>
                </div>
            </div>
        </nav>

        <main class="flex-1 h-screen overflow-y-auto relative">
            <div class="fixed top-4 right-4 z-50 w-96 pointer-events-none space-y-2">
                <div v-if="successMessage"
                    class="p-4 bg-green-50 text-green-700 border border-green-100 rounded-2xl shadow-sm pointer-events-auto">
                    <span class="font-bold">Success!</span> {{ successMessage }}
                </div>
                <div v-if="errorMessage"
                    class="p-4 bg-red-50 text-red-700 border border-red-100 rounded-2xl shadow-sm pointer-events-auto">
                    <span class="font-bold">Error!</span> {{ errorMessage }}
                </div>
            </div>

            <slot />
        </main>
    </div>
</template>
