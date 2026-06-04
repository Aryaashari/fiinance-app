<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import StatCard from '@/Components/StatCard.vue';
import ChartCard from '@/Components/ChartCard.vue';
import TransactionTable from '@/Components/TransactionTable.vue';
import { Bar, Line } from 'vue-chartjs';
import {
    Chart as ChartJS,
    CategoryScale, LinearScale, BarElement, PointElement, LineElement,
    Title, Tooltip, Legend, Filler
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, PointElement, LineElement, Title, Tooltip, Legend, Filler);

const props = defineProps({
    report: Object,
    filters: Object,
});

const reportType = ref(props.filters.type || 'monthly');
const selectedMonth = ref(props.filters.month || new Date().getMonth() + 1);
const selectedYear = ref(props.filters.year || new Date().getFullYear());
const selectedQuarter = ref(props.filters.quarter || Math.ceil((new Date().getMonth() + 1) / 3));

function loadReport() {
    const params = { type: reportType.value, year: selectedYear.value };
    if (reportType.value === 'monthly') params.month = selectedMonth.value;
    if (reportType.value === 'quarterly') params.quarter = selectedQuarter.value;
    router.get(route('reports.index'), params, { preserveState: false });
}

function exportReport(format) {
    const params = new URLSearchParams({ year: selectedYear.value });
    if (reportType.value === 'monthly') params.append('month', selectedMonth.value);
    if (reportType.value === 'quarterly') params.append('quarter', selectedQuarter.value);
    window.open(route('reports.export', { type: reportType.value, format }) + '?' + params.toString());
}

function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
}

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { labels: { color: '#94a3b8', font: { size: 11 } } },
        tooltip: {
            backgroundColor: '#1e293b',
            titleColor: '#f8fafc',
            bodyColor: '#cbd5e1',
            borderColor: '#475569',
            borderWidth: 1,
        }
    },
    scales: {
        x: { ticks: { color: '#94a3b8' }, grid: { color: '#1e293b' } },
        y: {
            ticks: {
                color: '#94a3b8',
                callback: (v) => `Rp ${(v / 1000000).toFixed(0)}M`
            },
            grid: { color: '#1e293b' }
        }
    }
};

const categoryChartData = computed(() => {
    const cats = props.report?.category_totals || [];
    return {
        labels: cats.map(c => c.category_name),
        datasets: [{
            label: 'Amount',
            data: cats.map(c => c.total_amount),
            backgroundColor: cats.map(c => c.category_color),
            borderRadius: 4,
        }]
    };
});

const months = [
    { value: 1, label: 'Jan' }, { value: 2, label: 'Feb' }, { value: 3, label: 'Mar' },
    { value: 4, label: 'Apr' }, { value: 5, label: 'May' }, { value: 6, label: 'Jun' },
    { value: 7, label: 'Jul' }, { value: 8, label: 'Aug' }, { value: 9, label: 'Sep' },
    { value: 10, label: 'Oct' }, { value: 11, label: 'Nov' }, { value: 12, label: 'Dec' },
];
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold text-surface-100">Reports</h1>
        </template>

        <PageHeader title="Financial Reports" subtitle="Generate and export financial reports.">
            <div class="flex items-center gap-2">
                <button
                    @click="exportReport('pdf')"
                    class="px-3 py-2 bg-danger-600/20 border border-danger-500/30 text-danger-400 hover:bg-danger-600/30 text-sm font-medium rounded-lg transition-colors"
                >
                    Export PDF
                </button>
                <button
                    @click="exportReport('csv')"
                    class="px-3 py-2 bg-success-600/20 border border-success-500/30 text-success-400 hover:bg-success-600/30 text-sm font-medium rounded-lg transition-colors"
                >
                    Export CSV
                </button>
            </div>
        </PageHeader>

        <!-- Report Type Selector -->
        <div class="rounded-xl border border-surface-700/50 bg-surface-800/60 p-4 mb-6">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-2">
                    <button
                        v-for="type in ['monthly', 'quarterly', 'yearly']"
                        :key="type"
                        @click="reportType = type"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-lg transition-colors capitalize',
                            reportType === type
                                ? 'bg-primary-500/15 text-primary-400 border border-primary-500/30'
                                : 'text-surface-400 border border-surface-700/50 hover:text-surface-200'
                        ]"
                    >
                        {{ type }}
                    </button>
                </div>

                <div class="flex items-center gap-2 ml-auto">
                    <select
                        v-if="reportType === 'monthly'"
                        v-model="selectedMonth"
                        class="bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm"
                    >
                        <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                    </select>

                    <select
                        v-if="reportType === 'quarterly'"
                        v-model="selectedQuarter"
                        class="bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm"
                    >
                        <option :value="1">Q1</option>
                        <option :value="2">Q2</option>
                        <option :value="3">Q3</option>
                        <option :value="4">Q4</option>
                    </select>

                    <select
                        v-model="selectedYear"
                        class="bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm"
                    >
                        <option v-for="y in [2024, 2025, 2026, 2027]" :key="y" :value="y">{{ y }}</option>
                    </select>

                    <button
                        @click="loadReport"
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-500 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        Generate
                    </button>
                </div>
            </div>
        </div>

        <!-- Report Content -->
        <div class="space-y-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <StatCard title="Total Income" :value="report?.totals?.total_income || 0" variant="success" />
                <StatCard title="Total Expense" :value="report?.totals?.total_expense || 0" variant="danger" />
                <StatCard
                    title="Net Cash Flow"
                    :value="report?.totals?.net_cash_flow || 0"
                    :variant="(report?.totals?.net_cash_flow || 0) >= 0 ? 'success' : 'danger'"
                />
            </div>

            <!-- Category Breakdown Chart -->
            <ChartCard title="Category Breakdown" :subtitle="report?.period || ''">
                <div class="h-64">
                    <Bar :data="categoryChartData" :options="chartOptions" />
                </div>
            </ChartCard>

            <!-- Transactions List -->
            <div>
                <h2 class="text-lg font-semibold text-surface-100 mb-4">Transaction Details</h2>
                <TransactionTable :transactions="report?.transactions || []" />
            </div>
        </div>
    </AppLayout>
</template>
