<script setup>
import { usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const page = usePage();
const show = ref(false);

// We watch the "flash" object inside Inertia's shared props
const flash = computed(() => page.props.flash);

let timer = null;

watch(flash, (newVal) => {
    if (newVal.success || newVal.error) {

        if(timer) clearTimeout(timer);

        show.value = true;
        // Auto-hide after 3 seconds
        timer = setTimeout(() => {
            show.value = false;
        }, 3000);
    }
}, { deep: true, immediate: true });
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show && (flash.success || flash.error)"
                 class="fixed top-5 right-5 z-[100] min-w-[300px]">

                <div :class="[
                    'px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 border',
                    flash.success ? 'bg-white border-green-100' : 'bg-white border-red-100'
                ]">
                    <div v-if="flash.success" class="bg-green-100 p-2 rounded-xl text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <div v-if="flash.error" class="bg-red-100 p-2 rounded-xl text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">
                            {{ flash.success ? 'Success' : 'System Error' }}
                        </p>
                        <p class="font-bold text-slate-800">{{ flash.success || flash.error }}</p>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
