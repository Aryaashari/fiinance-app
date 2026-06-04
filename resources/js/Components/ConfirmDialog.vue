<script setup>
defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: 'Confirm Action' },
    message: { type: String, default: 'Are you sure you want to proceed?' },
    confirmText: { type: String, default: 'Confirm' },
    cancelText: { type: String, default: 'Cancel' },
    variant: { type: String, default: 'danger' },
});

defineEmits(['confirm', 'cancel']);
</script>

<template>
    <Teleport to="body">
        <Transition leave-active-class="duration-200">
            <div v-show="show" class="relative z-50">
                <Transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-show="show" class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity" @click="$emit('cancel')" />
                </Transition>

                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <Transition
                            enter-active-class="ease-out duration-300"
                            enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                            leave-active-class="ease-in duration-200"
                            leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                            leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        >
                            <div v-show="show" class="relative transform overflow-hidden rounded-xl border border-surface-700/50 bg-surface-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full max-w-md mx-auto">
                                <div class="p-6">
                                    <div class="flex items-center gap-4">
                                        <div :class="['p-3 rounded-full', variant === 'danger' ? 'bg-danger-500/15' : 'bg-warning-500/15']">
                                            <svg :class="['w-6 h-6', variant === 'danger' ? 'text-danger-400' : 'text-warning-400']" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-surface-100">{{ title }}</h3>
                                            <p class="mt-1 text-sm text-surface-400">{{ message }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-6 py-4 border-t border-surface-700/50 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                                    <button
                                        @click="$emit('cancel')"
                                        class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-surface-300 bg-surface-700 hover:bg-surface-600 rounded-lg transition-colors"
                                    >
                                        {{ cancelText }}
                                    </button>
                                    <button
                                        @click="$emit('confirm')"
                                        :class="[
                                            'w-full sm:w-auto px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors',
                                            variant === 'danger' ? 'bg-danger-600 hover:bg-danger-500' : 'bg-warning-600 hover:bg-warning-500'
                                        ]"
                                    >
                                        {{ confirmText }}
                                    </button>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
