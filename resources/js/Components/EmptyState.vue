<script setup>
defineProps({
    title: { type: String, default: 'No data found' },
    description: { type: String, default: 'Get started by creating your first item.' },
    icon: { type: String, default: 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4' },
});
</script>

<template>
    <div class="flex flex-col items-center justify-center py-12 text-center">
        <div class="w-16 h-16 rounded-xl bg-surface-700/50 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-surface-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" :d="icon" />
            </svg>
        </div>
        <h3 class="text-sm font-medium text-surface-300">{{ title }}</h3>
        <p class="mt-1 text-xs text-surface-500 max-w-sm">{{ description }}</p>
        <div v-if="$slots.default" class="mt-4">
            <slot />
        </div>
    </div>
</template>
