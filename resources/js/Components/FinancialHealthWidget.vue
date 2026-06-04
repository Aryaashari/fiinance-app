<script setup>
import { computed } from 'vue';

const props = defineProps({
    score: { type: Number, required: true },
    insights: { type: Array, default: () => [] },
});

const scoreLabel = computed(() => {
    if (props.score >= 80) return 'Excellent';
    if (props.score >= 60) return 'Good';
    if (props.score >= 40) return 'Fair';
    return 'Needs Attention';
});

const scoreColor = computed(() => {
    if (props.score >= 80) return '#10b981';
    if (props.score >= 60) return '#3b82f6';
    if (props.score >= 40) return '#f59e0b';
    return '#ef4444';
});

const circumference = 2 * Math.PI * 40;
const offset = computed(() => circumference - (props.score / 100) * circumference);
</script>

<template>
    <div class="rounded-xl border border-surface-700/50 bg-surface-800/60 p-5">
        <h3 class="text-sm font-semibold text-surface-200 mb-4">Financial Health</h3>

        <div class="flex items-center gap-6">
            <!-- Gauge -->
            <div class="relative w-28 h-28 shrink-0">
                <svg class="w-28 h-28 -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="40" stroke="#334155" stroke-width="8" fill="none" />
                    <circle
                        cx="50" cy="50" r="40"
                        :stroke="scoreColor"
                        stroke-width="8"
                        fill="none"
                        stroke-linecap="round"
                        :stroke-dasharray="circumference"
                        :stroke-dashoffset="offset"
                        class="transition-all duration-1000 ease-out"
                    />
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-bold text-surface-50">{{ score }}</span>
                    <span class="text-[10px] text-surface-400 uppercase tracking-wider">Score</span>
                </div>
            </div>

            <!-- Info -->
            <div class="flex-1">
                <p class="text-lg font-semibold" :style="{ color: scoreColor }">{{ scoreLabel }}</p>
                <p class="text-xs text-surface-400 mt-1 leading-relaxed">
                    Your financial health score is based on savings rate, budget adherence, and cash flow.
                </p>
            </div>
        </div>
    </div>
</template>
