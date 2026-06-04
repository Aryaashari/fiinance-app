<script setup>
defineProps({
    allocations: { type: Array, required: true },
    totalPercentage: { type: Number, default: 0 },
});

function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
}
</script>

<template>
    <div class="space-y-4">
        <!-- Total progress -->
        <div class="rounded-xl border border-surface-700/50 bg-surface-800/60 p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-surface-300">Total Allocated</span>
                <span
                    :class="[
                        'text-sm font-bold',
                        totalPercentage > 100 ? 'text-danger-400' : totalPercentage === 100 ? 'text-success-400' : 'text-warning-400'
                    ]"
                >
                    {{ totalPercentage.toFixed(1) }}%
                </span>
            </div>
            <div class="w-full h-3 bg-surface-700 rounded-full overflow-hidden">
                <div
                    :class="[
                        'h-full rounded-full transition-all duration-500',
                        totalPercentage > 100 ? 'bg-danger-500' : totalPercentage === 100 ? 'bg-success-500' : 'bg-primary-500'
                    ]"
                    :style="{ width: Math.min(totalPercentage, 100) + '%' }"
                ></div>
            </div>
            <p class="mt-2 text-xs text-surface-500">
                {{ totalPercentage > 100 ? 'Over-allocated! Reduce percentages.' : (100 - totalPercentage).toFixed(1) + '% remaining' }}
            </p>
        </div>

        <!-- Individual allocations -->
        <div
            v-for="allocation in allocations"
            :key="allocation.id"
            class="rounded-lg border border-surface-700/30 bg-surface-800/30 p-3"
        >
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full" :style="{ backgroundColor: allocation.category?.color }"></div>
                    <span class="text-sm text-surface-200">{{ allocation.category?.name }}</span>
                </div>
                <span class="text-sm font-medium text-surface-300">{{ allocation.percentage }}%</span>
            </div>
            <div class="w-full h-1.5 bg-surface-700 rounded-full overflow-hidden">
                <div
                    class="h-full rounded-full transition-all duration-300"
                    :style="{
                        width: allocation.percentage + '%',
                        backgroundColor: allocation.category?.color || '#3b82f6'
                    }"
                ></div>
            </div>
        </div>

        <div v-if="allocations.length === 0" class="text-center py-8 text-surface-500 text-sm">
            No budget allocations set for this month.
        </div>
    </div>
</template>
