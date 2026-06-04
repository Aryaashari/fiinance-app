<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import TransactionTable from '@/Components/TransactionTable.vue';
import StatCard from '@/Components/StatCard.vue';
import ModalForm from '@/Components/ModalForm.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({
    transactions: Object,
    categories: Array,
    wallets: Array,
    monthlyTotals: Object,
    filters: Object,
});

const showModal = ref(false);
const showDeleteDialog = ref(false);
const editingTransaction = ref(null);
const deletingTransaction = ref(null);

const form = useForm({
    wallet_id: '',
    category_id: '',
    amount: '',
    transaction_date: new Date().toISOString().split('T')[0],
    notes: '',
});

// Filter state
const filterForm = useForm({
    type: props.filters?.type || '',
    category_id: props.filters?.category_id || '',
    date_from: props.filters?.date_from || '',
    date_to: props.filters?.date_to || '',
    search: props.filters?.search || '',
});

function applyFilters() {
    filterForm.get(route('transactions.index'), { preserveState: true });
}

function clearFilters() {
    filterForm.type = '';
    filterForm.category_id = '';
    filterForm.date_from = '';
    filterForm.date_to = '';
    filterForm.search = '';
    filterForm.get(route('transactions.index'));
}

function openCreate() {
    editingTransaction.value = null;
    form.reset();
    form.transaction_date = new Date().toISOString().split('T')[0];
    showModal.value = true;
}

function openEdit(transaction) {
    editingTransaction.value = transaction;
    form.wallet_id = transaction.wallet_id;
    form.category_id = transaction.category_id;
    form.amount = transaction.amount;
    form.transaction_date = transaction.transaction_date.split('T')[0];
    form.notes = transaction.notes || '';
    showModal.value = true;
}

function submit() {
    if (editingTransaction.value) {
        form.put(route('transactions.update', editingTransaction.value.id), {
            onSuccess: () => { showModal.value = false; form.reset(); },
        });
    } else {
        form.post(route('transactions.store'), {
            onSuccess: () => { showModal.value = false; form.reset(); },
        });
    }
}

function confirmDelete(transaction) {
    deletingTransaction.value = transaction;
    showDeleteDialog.value = true;
}

function deleteTransaction() {
    router.delete(route('transactions.destroy', deletingTransaction.value.id), {
        onSuccess: () => { showDeleteDialog.value = false; deletingTransaction.value = null; },
    });
}
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold text-surface-100">Transactions</h1>
        </template>

        <PageHeader title="Transaction Management" subtitle="Track all your income and expense transactions.">
            <button
                @click="openCreate"
                class="px-4 py-2 bg-primary-600 hover:bg-primary-500 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Transaction
            </button>
        </PageHeader>

        <!-- Stats Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <StatCard title="Income This Month" :value="monthlyTotals.total_income" variant="success" />
            <StatCard title="Expense This Month" :value="monthlyTotals.total_expense" variant="danger" />
            <StatCard title="Net Cash Flow" :value="monthlyTotals.net_cash_flow" :variant="monthlyTotals.net_cash_flow >= 0 ? 'success' : 'danger'" />
        </div>

        <!-- Filters -->
        <div class="rounded-xl border border-surface-700/50 bg-surface-800/60 p-4 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <select
                    v-model="filterForm.type"
                    class="bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500"
                >
                    <option value="">All Types</option>
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
                <select
                    v-model="filterForm.category_id"
                    class="bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500"
                >
                    <option value="">All Categories</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
                <input
                    v-model="filterForm.date_from"
                    type="date"
                    placeholder="From"
                    class="bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500"
                />
                <input
                    v-model="filterForm.date_to"
                    type="date"
                    placeholder="To"
                    class="bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500"
                />
                <div class="flex gap-2">
                    <button
                        @click="applyFilters"
                        class="flex-1 px-3 py-2 bg-primary-600 hover:bg-primary-500 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        Filter
                    </button>
                    <button
                        @click="clearFilters"
                        class="px-3 py-2 bg-surface-700 hover:bg-surface-600 text-surface-300 text-sm rounded-lg transition-colors"
                    >
                        Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <TransactionTable
            :transactions="transactions.data || []"
            @edit="openEdit"
            @delete="confirmDelete"
        />

        <!-- Pagination -->
        <div v-if="transactions.links && transactions.links.length > 3" class="mt-4 flex items-center justify-center gap-1">
            <template v-for="link in transactions.links" :key="link.label">
                <button
                    v-if="link.url"
                    @click="router.get(link.url)"
                    :class="[
                        'px-3 py-1.5 text-sm rounded-lg transition-colors',
                        link.active
                            ? 'bg-primary-600 text-white'
                            : 'bg-surface-700 text-surface-400 hover:text-surface-200 hover:bg-surface-600'
                    ]"
                    v-html="link.label"
                />
                <span v-else class="px-3 py-1.5 text-sm text-surface-600" v-html="link.label" />
            </template>
        </div>

        <!-- Create/Edit Modal -->
        <ModalForm
            :show="showModal"
            :title="editingTransaction ? 'Edit Transaction' : 'Add Transaction'"
            @close="showModal = false"
        >
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1">Wallet</label>
                        <select
                            v-model="form.wallet_id"
                            class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500"
                            required
                        >
                            <option value="">Select wallet</option>
                            <option v-for="w in wallets" :key="w.id" :value="w.id">{{ w.name }} (Rp {{ Number(w.balance).toLocaleString('id-ID') }})</option>
                        </select>
                        <p v-if="form.errors.wallet_id" class="mt-1 text-xs text-danger-400">{{ form.errors.wallet_id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1">Category</label>
                        <select
                            v-model="form.category_id"
                            class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500"
                            required
                        >
                            <option value="">Select category</option>
                            <optgroup label="Income">
                                <option v-for="cat in categories.filter(c => c.type === 'income')" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </optgroup>
                            <optgroup label="Expense">
                                <option v-for="cat in categories.filter(c => c.type === 'expense')" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </optgroup>
                        </select>
                        <p v-if="form.errors.category_id" class="mt-1 text-xs text-danger-400">{{ form.errors.category_id }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-1">Amount</label>
                    <input
                        v-model="form.amount"
                        type="number"
                        step="0.01"
                        min="0.01"
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500"
                        placeholder="0.00"
                        required
                    />
                    <p v-if="form.errors.amount" class="mt-1 text-xs text-danger-400">{{ form.errors.amount }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-1">Date</label>
                    <input
                        v-model="form.transaction_date"
                        type="date"
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500"
                        required
                    />
                    <p v-if="form.errors.transaction_date" class="mt-1 text-xs text-danger-400">{{ form.errors.transaction_date }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-1">Notes</label>
                    <textarea
                        v-model="form.notes"
                        rows="2"
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500"
                        placeholder="Optional notes"
                    />
                </div>
            </form>

            <template #footer>
                <button @click="showModal = false" class="px-4 py-2 text-sm font-medium text-surface-300 bg-surface-700 hover:bg-surface-600 rounded-lg transition-colors">
                    Cancel
                </button>
                <button
                    @click="submit"
                    :disabled="form.processing"
                    class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-500 rounded-lg transition-colors disabled:opacity-50"
                >
                    {{ editingTransaction ? 'Update' : 'Create' }}
                </button>
            </template>
        </ModalForm>

        <!-- Delete Confirm -->
        <ConfirmDialog
            :show="showDeleteDialog"
            title="Delete Transaction"
            message="Are you sure you want to delete this transaction? This action cannot be undone."
            confirm-text="Delete"
            @confirm="deleteTransaction"
            @cancel="showDeleteDialog = false"
        />
    </AppLayout>
</template>
