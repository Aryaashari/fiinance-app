<script setup>
import { computed } from 'vue';

const props = defineProps({
    investment: {
        type: Object,
        required: true,
    }
});

const formattedAllocated = computed(() => {
    return new Intl.NumberFormat('id-ID').format(props.investment.allocated_amount);
});

const formattedCurrent = computed(() => {
    return new Intl.NumberFormat('id-ID').format(props.investment.current_value);
});

const returnAmount = computed(() => {
    return props.investment.current_value - props.investment.allocated_amount;
});

const returnPercentage = computed(() => {
    if (!props.investment.allocated_amount) return 0;
    const pct = (returnAmount.value / props.investment.allocated_amount) * 100;
    return pct.toFixed(2);
});

const isPositive = computed(() => returnAmount.value >= 0);
</script>

<template>
    <div class="bg-surface-800 rounded-xl border border-surface-700 p-5 shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-start justify-between mb-4">
                <div>
                    <span class="inline-block px-2 py-1 text-[10px] font-semibold tracking-wide uppercase rounded-md mb-2"
                        :style="`background-color: ${investment.color || '#10b981'}20; color: ${investment.color || '#10b981'}`">
                        {{ investment.type }}
                    </span>
                    <h3 class="text-surface-100 font-semibold truncate">{{ investment.name }}</h3>
                </div>
                <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0"
                    :style="`background-color: ${investment.color || '#10b981'}20; color: ${investment.color || '#10b981'}`">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-surface-400">Allocated</span>
                    <span class="text-surface-200">Rp {{ formattedAllocated }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-surface-400">Current Value</span>
                    <span class="text-surface-100 font-medium">Rp {{ formattedCurrent }}</span>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-surface-700 flex justify-between items-center">
            <span class="text-xs text-surface-400">Total Return</span>
            <div class="flex items-center space-x-1" :class="isPositive ? 'text-success-500' : 'text-danger-500'">
                <svg v-if="isPositive" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                <span class="text-sm font-semibold">{{ isPositive ? '+' : '' }}{{ returnPercentage }}%</span>
            </div>
        </div>
    </div>
</template>
