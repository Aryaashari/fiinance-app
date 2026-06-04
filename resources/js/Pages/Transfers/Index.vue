<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import ModalForm from '@/Components/ModalForm.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({
    transfers: Object,
    wallets: Array,
});

const showModal = ref(false);
const showDeleteDialog = ref(false);
const deletingTransfer = ref(null);

const form = useForm({
    from_wallet_id: '',
    to_wallet_id: '',
    amount: '',
    transfer_date: new Date().toISOString().split('T')[0],
    notes: '',
});

function openCreate() {
    form.reset();
    form.transfer_date = new Date().toISOString().split('T')[0];
    showModal.value = true;
}

function submit() {
    form.post(route('transfers.store'), {
        onSuccess: () => { showModal.value = false; form.reset(); },
    });
}

function confirmDelete(transfer) {
    deletingTransfer.value = transfer;
    showDeleteDialog.value = true;
}

function deleteTransfer() {
    router.delete(route('transfers.destroy', deletingTransfer.value.id), {
        onSuccess: () => { showDeleteDialog.value = false; deletingTransfer.value = null; },
    });
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(amount);
}
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold text-surface-100">Transfers</h1>
        </template>

        <PageHeader title="Money Transfers" subtitle="Track money moving between your wallets.">
            <button
                @click="openCreate"
                class="px-4 py-2 bg-primary-600 hover:bg-primary-500 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2"
                :disabled="wallets.length < 2"
                :title="wallets.length < 2 ? 'You need at least 2 wallets to transfer' : ''"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                New Transfer
            </button>
        </PageHeader>

        <!-- Table -->
        <div class="bg-surface-800 border border-surface-700/50 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-surface-300">
                    <thead class="text-xs uppercase bg-surface-900/50 text-surface-400">
                        <tr>
                            <th class="px-6 py-4 font-medium">Date</th>
                            <th class="px-6 py-4 font-medium">From Wallet</th>
                            <th class="px-6 py-4 font-medium">To Wallet</th>
                            <th class="px-6 py-4 font-medium">Notes</th>
                            <th class="px-6 py-4 font-medium text-right">Amount</th>
                            <th class="px-6 py-4 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-700/50">
                        <tr v-for="transfer in transfers.data" :key="transfer.id" class="hover:bg-surface-700/20 transition-colors">
                            <td class="px-6 py-4 text-surface-200">
                                {{ formatDate(transfer.transfer_date) }}
                            </td>
                            <td class="px-6 py-4 font-medium text-surface-200">
                                {{ transfer.from_wallet?.name || 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 font-medium text-surface-200">
                                {{ transfer.to_wallet?.name || 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 text-surface-400 truncate max-w-[200px]" :title="transfer.notes">
                                {{ transfer.notes || '-' }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-surface-200">
                                {{ formatCurrency(transfer.amount) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button
                                    @click="confirmDelete(transfer)"
                                    class="p-2 text-surface-400 hover:text-danger-400 hover:bg-danger-500/10 rounded-lg transition-colors"
                                    title="Delete Transfer"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="transfers.data.length === 0">
                            <td colspan="6" class="px-6 py-8 text-center text-surface-500">
                                No transfers recorded.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div v-if="transfers.last_page > 1" class="px-6 py-4 border-t border-surface-700/50 flex justify-between items-center">
                <span class="text-sm text-surface-400">
                    Showing {{ transfers.from }} to {{ transfers.to }} of {{ transfers.total }} entries
                </span>
                <div class="flex gap-2">
                    <component
                        v-for="(link, i) in transfers.links"
                        :key="i"
                        :is="link.url ? 'a' : 'span'"
                        :href="link.url"
                        v-html="link.label"
                        :class="[
                            'px-3 py-1 rounded-md text-sm',
                            link.active ? 'bg-primary-600 text-white' : 'text-surface-400',
                            link.url ? 'hover:bg-surface-700 hover:text-surface-200' : 'opacity-50 cursor-not-allowed'
                        ]"
                        @click.prevent="link.url && router.visit(link.url)"
                    />
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <ModalForm
            :show="showModal"
            title="Record Transfer"
            @close="showModal = false"
        >
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1">From Wallet</label>
                        <select
                            v-model="form.from_wallet_id"
                            class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                            required
                        >
                            <option value="" disabled>Select Source</option>
                            <option v-for="w in wallets" :key="'from_'+w.id" :value="w.id" :disabled="form.to_wallet_id === w.id">{{ w.name }} (Rp {{ Number(w.balance).toLocaleString('id-ID') }})</option>
                        </select>
                        <p v-if="form.errors.from_wallet_id" class="mt-1 text-xs text-danger-400">{{ form.errors.from_wallet_id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1">To Wallet</label>
                        <select
                            v-model="form.to_wallet_id"
                            class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                            required
                        >
                            <option value="" disabled>Select Destination</option>
                            <option v-for="w in wallets" :key="'to_'+w.id" :value="w.id" :disabled="form.from_wallet_id === w.id">{{ w.name }}</option>
                        </select>
                        <p v-if="form.errors.to_wallet_id" class="mt-1 text-xs text-danger-400">{{ form.errors.to_wallet_id }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1">Amount</label>
                        <input
                            v-model="form.amount"
                            type="number"
                            step="0.01"
                            min="0.01"
                            class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                            required
                        />
                        <p v-if="form.errors.amount" class="mt-1 text-xs text-danger-400">{{ form.errors.amount }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1">Date</label>
                        <input
                            v-model="form.transfer_date"
                            type="date"
                            class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                            required
                        />
                        <p v-if="form.errors.transfer_date" class="mt-1 text-xs text-danger-400">{{ form.errors.transfer_date }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-1">Notes (Optional)</label>
                    <textarea
                        v-model="form.notes"
                        rows="2"
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="E.g. Move emergency fund to high yield savings"
                    />
                    <p v-if="form.errors.notes" class="mt-1 text-xs text-danger-400">{{ form.errors.notes }}</p>
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
                    :disabled="form.processing || !form.from_wallet_id || !form.to_wallet_id || !form.amount"
                    class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-500 rounded-lg transition-colors disabled:opacity-50"
                >
                    Transfer
                </button>
            </template>
        </ModalForm>

        <!-- Delete Confirm -->
        <ConfirmDialog
            :show="showDeleteDialog"
            title="Delete Transfer"
            message="Are you sure you want to delete this transfer? The balances of both wallets will be reverted to their previous state."
            confirm-text="Delete & Revert"
            @confirm="deleteTransfer"
            @cancel="showDeleteDialog = false"
        />
    </AppLayout>
</template>
