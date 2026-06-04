<script setup>
import { computed } from 'vue';

const props = defineProps({
    goal: {
        type: Object,
        required: true,
    }
});

const progress = computed(() => {
    if (!props.goal.target_amount) return 0;
    const pct = (props.goal.current_amount / props.goal.target_amount) * 100;
    return Math.min(Math.round(pct), 100);
});

const formattedCurrent = computed(() => {
    return new Intl.NumberFormat('id-ID').format(props.goal.current_amount);
});

const formattedTarget = computed(() => {
    return new Intl.NumberFormat('id-ID').format(props.goal.target_amount);
});
</script>

<template>
    <div class="bg-surface-800 rounded-xl border border-surface-700 p-5 shadow-sm">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h3 class="text-surface-100 font-semibold truncate">{{ goal.name }}</h3>
                <p class="text-surface-400 text-xs mt-1">{{ goal.notes }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0"
                :style="`background-color: ${goal.color || '#3b82f6'}20; color: ${goal.color || '#3b82f6'}`">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <div class="mb-2 flex justify-between text-sm">
            <span class="text-surface-300">Rp {{ formattedCurrent }}</span>
            <span class="text-surface-400">Rp {{ formattedTarget }}</span>
        </div>

        <div class="w-full bg-surface-700 rounded-full h-2.5 mb-2 overflow-hidden">
            <div class="h-2.5 rounded-full transition-all duration-500 ease-out"
                :style="`width: ${progress}%; background-color: ${goal.color || '#3b82f6'}`">
            </div>
        </div>
        
        <div class="flex justify-between items-center text-xs">
            <span class="font-medium" :style="`color: ${goal.color || '#3b82f6'}`">{{ progress }}%</span>
            <span class="text-surface-400" v-if="goal.target_date">Target: {{ new Date(goal.target_date).toLocaleDateString() }}</span>
        </div>
    </div>
</template>
