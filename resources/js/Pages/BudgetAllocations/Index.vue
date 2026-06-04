<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import BudgetSlider from '@/Components/BudgetSlider.vue';
import BudgetProgress from '@/Components/BudgetProgress.vue';
import ChartCard from '@/Components/ChartCard.vue';
import { Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps({
    allocations: Array,
    categories: Array,
    totalPercentage: Number,
    filters: Object,
});

const selectedMonth = ref(props.filters.month);
const selectedYear = ref(props.filters.year);

// Build local allocation state from props
const localAllocations = ref(
    props.categories.map(cat => {
        const existing = props.allocations.find(a => a.category_id === cat.id);
        return {
            category_id: cat.id,
            category_name: cat.name,
            category_color: cat.color,
            percentage: existing ? parseFloat(existing.percentage) : 0,
        };
    })
);

const totalPercentageLocal = computed(() =>
    localAllocations.value.reduce((sum, a) => sum + a.percentage, 0)
);

const isOverLimit = computed(() => totalPercentageLocal.value > 100);

const chartData = computed(() => ({
    labels: localAllocations.value.filter(a => a.percentage > 0).map(a => a.category_name),
    datasets: [{
        data: localAllocations.value.filter(a => a.percentage > 0).map(a => a.percentage),
        backgroundColor: localAllocations.value.filter(a => a.percentage > 0).map(a => a.category_color),
        borderWidth: 0,
    }],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: { color: '#94a3b8', font: { size: 11 }, padding: 12, usePointStyle: true },
        },
        tooltip: {
            backgroundColor: '#1e293b',
            titleColor: '#f8fafc',
            bodyColor: '#cbd5e1',
            callbacks: {
                label: (ctx) => `${ctx.label}: ${ctx.parsed}%`
            }
        }
    },
    cutout: '60%',
};

function saveBudget() {
    const form = useForm({
        month: selectedMonth.value,
        year: selectedYear.value,
        allocations: localAllocations.value
            .filter(a => a.percentage > 0)
            .map(a => ({
                category_id: a.category_id,
                percentage: a.percentage,
            })),
    });

    form.post(route('budget-allocations.bulk'));
}

function changeMonth() {
    router.get(route('budget-allocations.index'), {
        month: selectedMonth.value,
        year: selectedYear.value,
    }, { preserveState: false });
}

const months = [
    { value: 1, label: 'January' }, { value: 2, label: 'February' },
    { value: 3, label: 'March' }, { value: 4, label: 'April' },
    { value: 5, label: 'May' }, { value: 6, label: 'June' },
    { value: 7, label: 'July' }, { value: 8, label: 'August' },
    { value: 9, label: 'September' }, { value: 10, label: 'October' },
    { value: 11, label: 'November' }, { value: 12, label: 'December' },
];
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold text-surface-100">Budget Allocations</h1>
        </template>

        <PageHeader title="Budget Planner" subtitle="Define your monthly budget allocation percentages.">
            <button
                @click="saveBudget"
                :disabled="isOverLimit"
                :class="[
                    'px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors flex items-center gap-2',
                    isOverLimit ? 'bg-surface-600 cursor-not-allowed' : 'bg-primary-600 hover:bg-primary-500'
                ]"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Save Budget
            </button>
        </PageHeader>

        <!-- Month/Year Selector -->
        <div class="flex items-center gap-3 mb-6">
            <select
                v-model="selectedMonth"
                @change="changeMonth"
                class="bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500"
            >
                <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
            </select>
            <select
                v-model="selectedYear"
                @change="changeMonth"
                class="bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500"
            >
                <option v-for="y in [2024, 2025, 2026, 2027]" :key="y" :value="y">{{ y }}</option>
            </select>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sliders -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Total Bar -->
                <div class="rounded-xl border border-surface-700/50 bg-surface-800/60 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-surface-300">Total Allocation</span>
                        <span
                            :class="[
                                'text-lg font-bold',
                                isOverLimit ? 'text-danger-400' : totalPercentageLocal === 100 ? 'text-success-400' : 'text-primary-400'
                            ]"
                        >
                            {{ totalPercentageLocal.toFixed(1) }}%
                        </span>
                    </div>
                    <div class="w-full h-3 bg-surface-700 rounded-full overflow-hidden">
                        <div
                            :class="[
                                'h-full rounded-full transition-all duration-300',
                                isOverLimit ? 'bg-danger-500' : totalPercentageLocal === 100 ? 'bg-success-500' : 'bg-primary-500'
                            ]"
                            :style="{ width: Math.min(totalPercentageLocal, 100) + '%' }"
                        ></div>
                    </div>
                    <p v-if="isOverLimit" class="mt-2 text-xs text-danger-400">
                        Over-allocated by {{ (totalPercentageLocal - 100).toFixed(1) }}%. Please reduce allocations.
                    </p>
                    <p v-else class="mt-2 text-xs text-surface-500">
                        {{ (100 - totalPercentageLocal).toFixed(1) }}% remaining
                    </p>
                </div>

                <!-- Category Sliders -->
                <div class="rounded-xl border border-surface-700/50 bg-surface-800/60 p-5 space-y-5">
                    <BudgetSlider
                        v-for="allocation in localAllocations"
                        :key="allocation.category_id"
                        v-model="allocation.percentage"
                        :label="allocation.category_name"
                        :color="allocation.category_color"
                    />
                </div>
            </div>

            <!-- Visualization -->
            <div class="space-y-6">
                <ChartCard title="Allocation Visualization">
                    <div class="h-64">
                        <Doughnut v-if="localAllocations.some(a => a.percentage > 0)" :data="chartData" :options="chartOptions" />
                        <div v-else class="flex items-center justify-center h-full text-surface-500 text-sm">
                            Adjust sliders to see visualization
                        </div>
                    </div>
                </ChartCard>

                <BudgetProgress
                    :allocations="allocations"
                    :total-percentage="totalPercentage"
                />
            </div>
        </div>
    </AppLayout>
</template>
