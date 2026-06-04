<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    maxWidth: { type: String, default: 'lg' },
    title: { type: String, default: '' },
    closeable: { type: Boolean, default: true },
});

const emit = defineEmits(['close']);

const maxWidthClass = {
    sm: 'sm:max-w-sm',
    md: 'sm:max-w-md',
    lg: 'sm:max-w-lg',
    xl: 'sm:max-w-xl',
    '2xl': 'sm:max-w-2xl',
};

function close() {
    if (props.closeable) {
        emit('close');
    }
}
</script>

<template>
    <Teleport to="body">
        <Transition leave-active-class="duration-200">
            <div v-show="show" class="relative z-50">
                <!-- Overlay -->
                <Transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-show="show" class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity" @click="close" />
                </Transition>

                <!-- Modal Container -->
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
                            <div
                                v-show="show"
                                :class="['relative transform overflow-hidden rounded-xl border border-surface-700/50 bg-surface-800 text-left shadow-xl transition-all sm:my-8 sm:w-full w-full mx-auto', maxWidthClass[maxWidth]]"
                            >
                                <!-- Header -->
                                <div v-if="title" class="flex items-center justify-between px-6 py-4 border-b border-surface-700/50">
                                    <h3 class="text-lg font-semibold text-surface-100">{{ title }}</h3>
                                    <button v-if="closeable" @click="close" class="text-surface-400 hover:text-surface-200 transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Body -->
                                <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                                    <slot />
                                </div>

                                <!-- Footer -->
                                <div v-if="$slots.footer" class="px-6 py-4 border-t border-surface-700/50 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                                    <slot name="footer" />
                                </div>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
