<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import StatCard from '@/Components/StatCard.vue';
import ChartCard from '@/Components/ChartCard.vue';
import FinancialHealthWidget from '@/Components/FinancialHealthWidget.vue';
import InsightCard from '@/Components/InsightCard.vue';
import BudgetCard from '@/Components/BudgetCard.vue';
import { Bar, Line, Doughnut } from 'vue-chartjs';
import {
    Chart as ChartJS,
    CategoryScale, LinearScale, BarElement, PointElement, LineElement,
    Title, Tooltip, Legend, ArcElement, Filler
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, PointElement, LineElement, Title, Tooltip, Legend, ArcElement, Filler);

const props = defineProps({
    dashboardData: Object,
    monthlySummary: Array,
    year: Number,
});

const stats = props.dashboardData?.stats || {};
const charts = props.dashboardData?.charts || {};
const insights = props.dashboardData?.insights || [];
const budgetRealization = props.dashboardData?.budget_realization || [];

const healthScore = insights.find(i => i.title === 'Financial Health Score')?.value || 50;
const savingsRate = insights.find(i => i.title === 'Savings Rate')?.value || 0;

const chartDefaults = {
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

// Spending Trend (Line)
const spendingTrendData = {
    labels: charts.income_vs_expense?.labels || [],
    datasets: [
        {
            label: 'Income',
            data: charts.income_vs_expense?.income || [],
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.08)',
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#10b981',
        },
        {
            label: 'Expense',
            data: charts.income_vs_expense?.expense || [],
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.08)',
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#ef4444',
        },
    ],
};

// Expense Distribution
const expenseDistData = {
    labels: (charts.expense_distribution || []).map(d => d.label),
    datasets: [{
        data: (charts.expense_distribution || []).map(d => d.value),
        backgroundColor: (charts.expense_distribution || []).map(d => d.color),
        borderWidth: 0,
    }],
};

const doughnutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'right', labels: { color: '#94a3b8', font: { size: 11 }, padding: 10, usePointStyle: true } },
        tooltip: {
            backgroundColor: '#1e293b',
            titleColor: '#f8fafc',
            bodyColor: '#cbd5e1',
            callbacks: { label: (ctx) => `${ctx.label}: Rp ${new Intl.NumberFormat('id-ID').format(ctx.parsed)}` }
        }
    },
    cutout: '65%',
};

// Cash Flow Bars
const cashFlowData = {
    labels: charts.monthly_cash_flow?.labels || [],
    datasets: [{
        label: 'Net Cash Flow',
        data: charts.monthly_cash_flow?.data || [],
        backgroundColor: (charts.monthly_cash_flow?.data || []).map(v => v >= 0 ? 'rgba(16, 185, 129, 0.6)' : 'rgba(239, 68, 68, 0.6)'),
        borderRadius: 4,
    }],
};

function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
}
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold text-surface-100">Analytics</h1>
        </template>

        <PageHeader title="Financial Analytics" :subtitle="`Detailed financial analysis for ${year}`" />

        <div class="space-y-6">
            <!-- Key Metrics -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <StatCard title="Total Income" :value="stats.total_income || 0" variant="success" />
                <StatCard title="Total Expense" :value="stats.total_expense || 0" variant="danger" />
                <StatCard title="Savings Rate" :value="savingsRate" format="percentage" :variant="savingsRate >= 20 ? 'success' : 'warning'" />
                <StatCard title="Health Score" :value="healthScore" format="number" :variant="healthScore >= 60 ? 'success' : 'warning'" />
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <ChartCard title="Income vs Expense Trend" subtitle="12-month overview">
                    <div class="h-72">
                        <Line :data="spendingTrendData" :options="chartDefaults" />
                    </div>
                </ChartCard>
                <ChartCard title="Expense Distribution" subtitle="Current month">
                    <div class="h-72">
                        <Doughnut :data="expenseDistData" :options="doughnutOptions" />
                    </div>
                </ChartCard>
            </div>

            <!-- Cash Flow -->
            <ChartCard title="Monthly Cash Flow" subtitle="Net income by month">
                <div class="h-64">
                    <Bar :data="cashFlowData" :options="chartDefaults" />
                </div>
            </ChartCard>

            <!-- Health + Insights -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <FinancialHealthWidget :score="healthScore" />

                <div class="lg:col-span-2 space-y-4">
                    <h2 class="text-lg font-semibold text-surface-100">Insights</h2>
                    <InsightCard
                        v-for="(insight, i) in insights"
                        :key="i"
                        :type="insight.type"
                        :title="insight.title"
                        :message="insight.message"
                        :value="insight.value"
                        :categories="insight.categories || []"
                    />
                </div>
            </div>

            <!-- Budget Performance -->
            <div>
                <h2 class="text-lg font-semibold text-surface-100 mb-4">Budget Performance</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <BudgetCard
                        v-for="item in budgetRealization"
                        :key="item.category_id"
                        :category-name="item.category_name"
                        :category-color="item.category_color"
                        :percentage="item.percentage"
                        :budget-amount="item.budget_amount"
                        :actual-amount="item.actual_amount"
                        :usage-percentage="item.usage_percentage"
                        :is-over-budget="item.is_over_budget"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
