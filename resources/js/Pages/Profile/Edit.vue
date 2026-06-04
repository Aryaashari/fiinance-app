<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold text-surface-100">Profile</h1>
        </template>

        <div class="max-w-3xl space-y-6">
            <div class="bg-surface-800/60 border border-surface-700/50 p-6 rounded-xl">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div class="bg-surface-800/60 border border-surface-700/50 p-6 rounded-xl">
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <div class="bg-surface-800/60 border border-surface-700/50 p-6 rounded-xl">
                <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </AppLayout>
</template>
