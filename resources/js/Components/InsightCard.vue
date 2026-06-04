<script setup>
defineProps({
    type: { type: String, default: 'info' }, // info, success, warning, caution
    title: { type: String, required: true },
    message: { type: String, required: true },
    value: { type: [Number, String], default: null },
    categories: { type: Array, default: () => [] },
});

const typeConfig = {
    info: { bg: 'bg-info-500/10', border: 'border-info-500/30', icon: 'text-info-400', iconPath: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    success: { bg: 'bg-success-500/10', border: 'border-success-500/30', icon: 'text-success-400', iconPath: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
    warning: { bg: 'bg-warning-500/10', border: 'border-warning-500/30', icon: 'text-warning-400', iconPath: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z' },
    caution: { bg: 'bg-danger-500/10', border: 'border-danger-500/30', icon: 'text-danger-400', iconPath: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
};
</script>

<template>
    <div :class="['rounded-xl border p-4', typeConfig[type].bg, typeConfig[type].border]">
        <div class="flex items-start gap-3">
            <svg :class="['w-5 h-5 shrink-0 mt-0.5', typeConfig[type].icon]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" :d="typeConfig[type].iconPath" />
            </svg>
            <div class="flex-1 min-w-0">
                <h4 class="text-sm font-semibold text-surface-200">{{ title }}</h4>
                <p class="mt-1 text-xs text-surface-400 leading-relaxed">{{ message }}</p>
                <div v-if="categories.length > 0" class="mt-2 flex flex-wrap gap-1.5">
                    <span
                        v-for="cat in categories"
                        :key="cat"
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-surface-700/50 text-surface-300"
                    >
                        {{ cat }}
                    </span>
                </div>
                <div v-if="value !== null && title.includes('Rate')" class="mt-2">
                    <div class="w-full h-1.5 bg-surface-700 rounded-full overflow-hidden">
                        <div
                            :class="['h-full rounded-full transition-all duration-500', type === 'success' ? 'bg-success-500' : type === 'warning' ? 'bg-warning-500' : 'bg-info-500']"
                            :style="{ width: Math.min(Math.max(value, 0), 100) + '%' }"
                        ></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
