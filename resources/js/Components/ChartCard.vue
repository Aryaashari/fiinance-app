<script setup>
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    loading: { type: Boolean, default: false },
});
</script>

<template>
    <div class="rounded-xl border border-surface-700/50 bg-surface-800/60 backdrop-blur-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 pt-5 pb-3">
            <div>
                <h3 class="text-sm font-semibold text-surface-200">{{ title }}</h3>
                <p v-if="subtitle" class="text-xs text-surface-500 mt-0.5">{{ subtitle }}</p>
            </div>
            <slot name="actions" />
        </div>
        <div class="px-5 pb-5">
            <div v-if="loading" class="flex items-center justify-center h-48">
                <div class="w-8 h-8 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
            <slot v-else />
        </div>
    </div>
</template>
