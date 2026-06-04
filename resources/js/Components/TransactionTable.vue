<script setup>
defineProps({
    transactions: { type: Array, required: true },
    showPagination: { type: Boolean, default: false },
    paginationLinks: { type: Object, default: null },
});

defineEmits(['edit', 'delete']);

function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}
</script>

<template>
    <div class="overflow-hidden rounded-xl border border-surface-700/50">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-surface-700/50 bg-surface-800/80">
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-400">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-400">Wallet</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-400">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-400">Type</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-surface-400">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-400">Notes</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-surface-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-700/30">
                    <tr
                        v-for="transaction in transactions"
                        :key="transaction.id"
                        class="bg-surface-800/40 hover:bg-surface-700/40 transition-colors"
                    >
                        <td class="px-4 py-3 text-surface-300 whitespace-nowrap">{{ formatDate(transaction.transaction_date) }}</td>
                        <td class="px-4 py-3 text-surface-300 whitespace-nowrap">{{ transaction.wallet?.name || '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 rounded-full" :style="{ backgroundColor: transaction.category?.color || '#94a3b8' }"></div>
                                <span class="text-surface-200">{{ transaction.category?.name || 'Unknown' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span
                                :class="[
                                    'inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium',
                                    transaction.category?.type === 'income'
                                        ? 'bg-success-500/15 text-success-400'
                                        : 'bg-danger-500/15 text-danger-400'
                                ]"
                            >
                                {{ transaction.category?.type === 'income' ? 'Income' : 'Expense' }}
                            </span>
                        </td>
                        <td
                            :class="[
                                'px-4 py-3 text-right font-medium whitespace-nowrap',
                                transaction.category?.type === 'income' ? 'text-success-400' : 'text-danger-400'
                            ]"
                        >
                            {{ transaction.category?.type === 'income' ? '+' : '-' }}{{ formatCurrency(transaction.amount) }}
                        </td>
                        <td class="px-4 py-3 text-surface-400 max-w-48 truncate">{{ transaction.notes || '-' }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <button
                                @click="$emit('edit', transaction)"
                                class="text-primary-400 hover:text-primary-300 transition-colors p-1"
                                title="Edit"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button
                                @click="$emit('delete', transaction)"
                                class="text-danger-400 hover:text-danger-300 transition-colors p-1 ml-1"
                                title="Delete"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <tr v-if="transactions.length === 0">
                        <td colspan="7" class="px-4 py-12 text-center text-surface-500 bg-surface-800/40">
                            No transactions found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
