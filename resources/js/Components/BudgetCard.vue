<script setup>
const props = defineProps({
    categoryName: { type: String, required: true },
    categoryColor: { type: String, default: '#3b82f6' },
    percentage: { type: Number, required: true },
    budgetAmount: { type: Number, required: true },
    actualAmount: { type: Number, required: true },
    usagePercentage: { type: Number, default: 0 },
    isOverBudget: { type: Boolean, default: false },
});

function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
}

function getProgressColor() {
    if (props.isOverBudget) return 'bg-danger-500';
    if (props.usagePercentage >= 80) return 'bg-warning-500';
    return 'bg-success-500';
}
</script>

<template>
    <div class="rounded-xl border border-surface-700/50 bg-surface-800/60 p-4 transition-all duration-200 hover:border-surface-600/50">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: categoryColor }"></div>
                <span class="text-sm font-medium text-surface-200">{{ categoryName }}</span>
            </div>
            <span class="text-xs font-medium text-surface-400">{{ percentage }}%</span>
        </div>

        <!-- Progress bar -->
        <div class="w-full h-2 bg-surface-700 rounded-full overflow-hidden mb-3">
            <div
                :class="['h-full rounded-full transition-all duration-500 ease-out', getProgressColor()]"
                :style="{ width: Math.min(usagePercentage, 100) + '%' }"
            ></div>
        </div>

        <div class="flex items-center justify-between text-xs">
            <div>
                <span class="text-surface-400">Spent: </span>
                <span :class="isOverBudget ? 'text-danger-400' : 'text-surface-200'">{{ formatCurrency(actualAmount) }}</span>
            </div>
            <div>
                <span class="text-surface-400">Budget: </span>
                <span class="text-surface-300">{{ formatCurrency(budgetAmount) }}</span>
            </div>
        </div>

        <div v-if="isOverBudget" class="mt-2 text-xs text-danger-400 flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            Over budget by {{ formatCurrency(actualAmount - budgetAmount) }}
        </div>
    </div>
</template>
