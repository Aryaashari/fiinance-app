<script setup>
defineProps({
    modelValue: { type: Number, default: 0 },
    min: { type: Number, default: 0 },
    max: { type: Number, default: 100 },
    step: { type: Number, default: 1 },
    label: { type: String, default: '' },
    color: { type: String, default: '#3b82f6' },
    disabled: { type: Boolean, default: false },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full" :style="{ backgroundColor: color }"></div>
                <label class="text-sm font-medium text-surface-200">{{ label }}</label>
            </div>
            <div class="flex items-center gap-2">
                <input
                    type="number"
                    :value="modelValue"
                    @input="$emit('update:modelValue', parseFloat($event.target.value) || 0)"
                    :min="min"
                    :max="max"
                    :step="step"
                    :disabled="disabled"
                    class="w-16 text-right text-sm bg-surface-700 border border-surface-600 rounded-md px-2 py-1 text-surface-200 focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                />
                <span class="text-xs text-surface-400">%</span>
            </div>
        </div>
        <input
            type="range"
            :value="modelValue"
            @input="$emit('update:modelValue', parseFloat($event.target.value))"
            :min="min"
            :max="max"
            :step="step"
            :disabled="disabled"
            class="w-full h-2 rounded-full appearance-none cursor-pointer bg-surface-700"
            :style="{
                background: `linear-gradient(to right, ${color} 0%, ${color} ${modelValue}%, #334155 ${modelValue}%, #334155 100%)`
            }"
        />
    </div>
</template>

<style scoped>
input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: white;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}
input[type="range"]::-moz-range-thumb {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: white;
    cursor: pointer;
    border: none;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}
</style>
