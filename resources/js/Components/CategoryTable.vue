<script setup>
defineProps({
    categories: { type: Array, required: true },
});

defineEmits(['edit', 'delete', 'toggle']);
</script>

<template>
    <div class="overflow-hidden rounded-xl border border-surface-700/50">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-surface-700/50 bg-surface-800/80">
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-400">Color</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-400">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-400">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-400">Description</th>
                        <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-surface-400">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-surface-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-700/30">
                    <tr
                        v-for="category in categories"
                        :key="category.id"
                        class="bg-surface-800/40 hover:bg-surface-700/40 transition-colors"
                    >
                        <td class="px-4 py-3">
                            <div class="w-6 h-6 rounded-lg" :style="{ backgroundColor: category.color }"></div>
                        </td>
                        <td class="px-4 py-3 font-medium text-surface-200">{{ category.name }}</td>
                        <td class="px-4 py-3">
                            <span
                                :class="[
                                    'inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium',
                                    category.type === 'income'
                                        ? 'bg-success-500/15 text-success-400'
                                        : 'bg-danger-500/15 text-danger-400'
                                ]"
                            >
                                {{ category.type === 'income' ? 'Income' : 'Expense' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-surface-400 max-w-64 truncate">{{ category.description || '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <button
                                @click="$emit('toggle', category)"
                                :class="[
                                    'relative inline-flex h-5 w-9 items-center rounded-full transition-colors duration-200',
                                    category.is_active ? 'bg-success-500' : 'bg-surface-600'
                                ]"
                            >
                                <span
                                    :class="[
                                        'inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform duration-200',
                                        category.is_active ? 'translate-x-4.5' : 'translate-x-1'
                                    ]"
                                />
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <button
                                @click="$emit('edit', category)"
                                class="text-primary-400 hover:text-primary-300 transition-colors p-1"
                                title="Edit"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button
                                @click="$emit('delete', category)"
                                class="text-danger-400 hover:text-danger-300 transition-colors p-1 ml-1"
                                title="Delete"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <tr v-if="categories.length === 0">
                        <td colspan="6" class="px-4 py-12 text-center text-surface-500 bg-surface-800/40">
                            No categories found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
