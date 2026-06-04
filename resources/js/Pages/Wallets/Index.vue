<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ModalForm from '@/Components/ModalForm.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({
    wallets: Array,
});

const showModal = ref(false);
const showDeleteDialog = ref(false);
const editingWallet = ref(null);
const deletingWallet = ref(null);

const form = useForm({
    name: '',
    type: 'bank',
    balance: 0,
    is_active: true,
});

function openCreate() {
    editingWallet.value = null;
    form.reset();
    form.type = 'bank';
    form.balance = 0;
    form.is_active = true;
    showModal.value = true;
}

function openEdit(wallet) {
    editingWallet.value = wallet;
    form.name = wallet.name;
    form.type = wallet.type;
    form.balance = wallet.balance;
    form.is_active = wallet.is_active;
    showModal.value = true;
}

function submit() {
    if (editingWallet.value) {
        form.put(route('wallets.update', editingWallet.value.id), {
            onSuccess: () => { showModal.value = false; form.reset(); },
        });
    } else {
        form.post(route('wallets.store'), {
            onSuccess: () => { showModal.value = false; form.reset(); },
        });
    }
}

function confirmDelete(wallet) {
    deletingWallet.value = wallet;
    showDeleteDialog.value = true;
}

function deleteWallet() {
    router.delete(route('wallets.destroy', deletingWallet.value.id), {
        onSuccess: () => { showDeleteDialog.value = false; deletingWallet.value = null; },
    });
}
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold text-surface-100">Wallets</h1>
        </template>

        <PageHeader title="Wallet Management" subtitle="Manage your banks, e-wallets, and cash.">
            <button
                @click="openCreate"
                class="px-4 py-2 bg-primary-600 hover:bg-primary-500 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Wallet
            </button>
        </PageHeader>

        <!-- Table -->
        <div class="bg-surface-800 border border-surface-700/50 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-surface-300">
                    <thead class="text-xs uppercase bg-surface-900/50 text-surface-400">
                        <tr>
                            <th class="px-6 py-4 font-medium">Name</th>
                            <th class="px-6 py-4 font-medium">Type</th>
                            <th class="px-6 py-4 font-medium text-right">Balance</th>
                            <th class="px-6 py-4 font-medium text-center">Status</th>
                            <th class="px-6 py-4 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-700/50">
                        <tr v-for="wallet in wallets" :key="wallet.id" class="hover:bg-surface-700/20 transition-colors">
                            <td class="px-6 py-4 font-medium text-surface-200">
                                {{ wallet.name }}
                            </td>
                            <td class="px-6 py-4 capitalize">
                                {{ wallet.type }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium">
                                Rp {{ Number(wallet.balance).toLocaleString('id-ID') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span :class="[
                                    'px-2 py-1 text-xs font-medium rounded-full',
                                    wallet.is_active ? 'bg-success-500/10 text-success-400' : 'bg-surface-600 text-surface-300'
                                ]">
                                    {{ wallet.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        @click="openEdit(wallet)"
                                        class="p-2 text-surface-400 hover:text-primary-400 hover:bg-primary-500/10 rounded-lg transition-colors"
                                        title="Edit"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button
                                        @click="confirmDelete(wallet)"
                                        class="p-2 text-surface-400 hover:text-danger-400 hover:bg-danger-500/10 rounded-lg transition-colors"
                                        title="Delete"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="wallets.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-surface-500">
                                No wallets found. Create one to start tracking places!
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <ModalForm
            :show="showModal"
            :title="editingWallet ? 'Edit Wallet' : 'Create Wallet'"
            @close="showModal = false"
        >
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-1">Name</label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="e.g. Bank BCA, OVO, Cash"
                        required
                    />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-danger-400">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-1">Type</label>
                    <select
                        v-model="form.type"
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                        required
                    >
                        <option value="bank">Bank Account</option>
                        <option value="ewallet">E-Wallet</option>
                        <option value="cash">Cash</option>
                        <option value="other">Other</option>
                    </select>
                    <p v-if="form.errors.type" class="mt-1 text-xs text-danger-400">{{ form.errors.type }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-1">Current Balance</label>
                    <input
                        v-model="form.balance"
                        type="number"
                        step="0.01"
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                        required
                    />
                    <p v-if="form.errors.balance" class="mt-1 text-xs text-danger-400">{{ form.errors.balance }}</p>
                </div>
                
                <div class="flex items-center gap-2 mt-4">
                    <input
                        type="checkbox"
                        id="is_active"
                        v-model="form.is_active"
                        class="rounded border-surface-600 bg-surface-700 text-primary-500 focus:ring-primary-500"
                    />
                    <label for="is_active" class="text-sm font-medium text-surface-300">Active Wallet</label>
                </div>
            </form>

            <template #footer>
                <button
                    @click="showModal = false"
                    class="px-4 py-2 text-sm font-medium text-surface-300 bg-surface-700 hover:bg-surface-600 rounded-lg transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="submit"
                    :disabled="form.processing"
                    class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-500 rounded-lg transition-colors disabled:opacity-50"
                >
                    {{ editingWallet ? 'Update' : 'Create' }}
                </button>
            </template>
        </ModalForm>

        <!-- Delete Confirm -->
        <ConfirmDialog
            :show="showDeleteDialog"
            title="Delete Wallet"
            :message="`Are you sure you want to delete '${deletingWallet?.name}'? This action cannot be undone.`"
            confirm-text="Delete"
            @confirm="deleteWallet"
            @cancel="showDeleteDialog = false"
        />
    </AppLayout>
</template>
