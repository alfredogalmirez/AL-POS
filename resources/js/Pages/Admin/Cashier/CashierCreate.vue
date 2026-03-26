<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    username: '',
    password: '',
    password_confirmation: '',
    role: 'cashier',
});

const submit = () => {
    form.post(route('admin.cashier.store'), {
        onFinish: () => form.reset('password', 'confirm_password')
    });
}

</script>
<template>
    <AdminLayout>
        <form @submit.prevent="submit">
            <div>
            <label>Full Name</label>
            <input v-model="form.name" text="text">
            <p v-if="form.errors.name">{{ form.errors.name }}</p>
            </div>

            <div>
            <label>Username</label>
            <input v-model="form.username" type="text">
            <p v-if="form.errors.username">{{ form.errors.username }}</p>
            </div>

            <div>
            <label>Password</label>
            <input v-model="form.password" type="password">
            <p v-if="form.errors.password">{{ form.errors.password }}</p>
            </div>

            <div>
            <label>Confirm Password</label>
            <input v-model="form.password_confirmation" type="password">
            <p v-if="form.errors.password_confirmation">{{ form.errors.password_confirmation }}</p>
            </div>

            <div>
            <label>Role</label>
            <input v-model="form.role" text="text">
            <p v-if="form.errors.role">{{ form.errors.role }}</p>
            </div>

            <div>
                <button type="submit">
                    Add Cashier
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
