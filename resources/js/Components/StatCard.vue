<script setup>
defineProps({
    title: { type: String, required: true },
    value: { type: [String, Number], required: true },
    subtitle: { type: String, default: '' },
    trend: { type: Number, default: null },
    icon: { type: String, default: '' },
    variant: { type: String, default: 'default' }, // default, success, danger, warning, info
    format: { type: String, default: 'currency' }, // currency, number, percentage
});

function formatValue(value, format) {
    if (format === 'currency') {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
    }
    if (format === 'percentage') {
        return `${value}%`;
    }
    return new Intl.NumberFormat('id-ID').format(value);
}

const variantClasses = {
    default: 'from-surface-700/80 to-surface-800/80 border-surface-600/50',
    success: 'from-success-600/15 to-success-700/10 border-success-500/30',
    danger: 'from-danger-600/15 to-danger-700/10 border-danger-500/30',
    warning: 'from-warning-600/15 to-warning-700/10 border-warning-500/30',
    info: 'from-info-600/15 to-info-700/10 border-info-500/30',
};

const iconClasses = {
    default: 'text-surface-400',
    success: 'text-success-400',
    danger: 'text-danger-400',
    warning: 'text-warning-400',
    info: 'text-info-400',
};
</script>

<template>
    <div
        :class="[
            'relative overflow-hidden rounded-xl border bg-gradient-to-br p-5 transition-all duration-300 hover:shadow-lg hover:shadow-black/10 hover:-translate-y-0.5',
            variantClasses[variant]
        ]"
    >
        <div class="flex items-start justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium uppercase tracking-wider text-surface-400">{{ title }}</p>
                <p class="mt-2 text-2xl font-bold text-surface-50 truncate">{{ formatValue(value, format) }}</p>
                <p v-if="subtitle" class="mt-1 text-xs text-surface-400">{{ subtitle }}</p>
            </div>
            <div v-if="icon" :class="['p-2.5 rounded-lg bg-surface-800/50', iconClasses[variant]]">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" :d="icon" />
                </svg>
            </div>
        </div>

        <!-- Trend indicator -->
        <div v-if="trend !== null" class="mt-3 flex items-center gap-1">
            <svg
                :class="[
                    'w-4 h-4',
                    trend >= 0 ? 'text-success-400' : 'text-danger-400'
                ]"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
            >
                <path
                    stroke-linecap="round" stroke-linejoin="round"
                    :d="trend >= 0 ? 'M7 17l9.2-9.2M17 17V7H7' : 'M17 7l-9.2 9.2M7 7v10h10'"
                />
            </svg>
            <span :class="['text-xs font-medium', trend >= 0 ? 'text-success-400' : 'text-danger-400']">
                {{ Math.abs(trend) }}%
            </span>
            <span class="text-xs text-surface-500">vs last month</span>
        </div>
    </div>
</template>
