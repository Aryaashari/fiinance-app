<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import ChartCard from '@/Components/ChartCard.vue';
import BudgetCard from '@/Components/BudgetCard.vue';
import SavingsGoalCard from '@/Components/SavingsGoalCard.vue';
import InvestmentCard from '@/Components/InvestmentCard.vue';
import TransactionTable from '@/Components/TransactionTable.vue';
import FinancialHealthWidget from '@/Components/FinancialHealthWidget.vue';
import InsightCard from '@/Components/InsightCard.vue';
import { Bar, Doughnut, Line } from 'vue-chartjs';
import {
    Chart as ChartJS,
    CategoryScale, LinearScale, BarElement, PointElement, LineElement,
    Title, Tooltip, Legend, ArcElement, Filler
} from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, PointElement, LineElement, Title, Tooltip, Legend, ArcElement, Filler);

const props = defineProps({
    stats: Object,
    charts: Object,
    insights: Array,
    recent_transactions: Array,
    budget_allocations: Array,
    savings_goals: Array,
    investments: Array,
    top_spending: Array,
    current_month: Number,
    current_year: Number,
});

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
            padding: 12,
            callbacks: {
                label: (ctx) => {
                    return `${ctx.dataset.label}: Rp ${new Intl.NumberFormat('id-ID').format(ctx.parsed.y || ctx.parsed)}`;
                }
            }
        }
    },
    scales: {
        x: { ticks: { color: '#94a3b8', font: { size: 10 } }, grid: { color: '#1e293b' } },
        y: {
            ticks: {
                color: '#94a3b8',
                font: { size: 10 },
                callback: (v) => `Rp ${(v / 1000000).toFixed(0)}M`
            },
            grid: { color: '#1e293b' }
        }
    }
};

const incomeExpenseData = {
    labels: props.charts?.income_vs_expense?.labels || [],
    datasets: [
        {
            label: 'Income',
            data: props.charts?.income_vs_expense?.income || [],
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 3,
        },
        {
            label: 'Expense',
            data: props.charts?.income_vs_expense?.expense || [],
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 3,
        },
    ],
};

const expenseDistData = {
    labels: (props.charts?.expense_distribution || []).map(d => d.label),
    datasets: [{
        data: (props.charts?.expense_distribution || []).map(d => d.value),
        backgroundColor: (props.charts?.expense_distribution || []).map(d => d.color),
        borderWidth: 0,
    }],
};

const cashFlowData = {
    labels: props.charts?.monthly_cash_flow?.labels || [],
    datasets: [{
        label: 'Net Cash Flow',
        data: props.charts?.monthly_cash_flow?.data || [],
        backgroundColor: (props.charts?.monthly_cash_flow?.data || []).map(v => v >= 0 ? 'rgba(16, 185, 129, 0.6)' : 'rgba(239, 68, 68, 0.6)'),
        borderRadius: 4,
    }],
};

const doughnutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'right', labels: { color: '#94a3b8', font: { size: 11 }, padding: 12, usePointStyle: true } },
        tooltip: {
            backgroundColor: '#1e293b',
            titleColor: '#f8fafc',
            bodyColor: '#cbd5e1',
            borderColor: '#475569',
            borderWidth: 1,
            callbacks: {
                label: (ctx) => `${ctx.label}: Rp ${new Intl.NumberFormat('id-ID').format(ctx.parsed)}`
            }
        }
    },
    cutout: '65%',
};

const healthScore = props.insights?.find(i => i.title === 'Financial Health Score')?.value || 50;

// Format Budget Data for BudgetCard
const formattedBudgets = computed(() => {
    return (props.budget_allocations || []).map(budget => {
        // Here we just map the new budget allocation format to what BudgetCard expects roughly
        // We'll treat 'actual_amount' as 0 since it's just planning for now
        return {
            category_id: budget.id,
            category_name: budget.name,
            category_color: budget.color || '#3b82f6',
            percentage: budget.amount_type === 'percentage' ? budget.amount : null,
            budget_amount: budget.calculated_budget,
            actual_amount: 0, // Placeholder
            usage_percentage: 0,
            is_over_budget: false
        }
    });
});
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold text-surface-100">Financial Dashboard</h1>
        </template>

        <div class="space-y-12">
            <!-- Section 1: Financial Overview -->
            <section>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-surface-100 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Financial Overview
                    </h2>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <StatCard title="Total Income" :value="stats.total_income" variant="success" icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    <StatCard title="Total Expense" :value="stats.total_expense" variant="danger" icon="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    <StatCard title="Net Cash Flow" :value="stats.net_cash_flow" :variant="stats.net_cash_flow >= 0 ? 'success' : 'danger'" icon="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    <StatCard title="Remaining Budget" :value="stats.remaining_budget" :variant="stats.remaining_budget >= 0 ? 'info' : 'warning'" icon="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <ChartCard title="Income vs Expense Trend" subtitle="Monthly overview">
                        <div class="h-64"><Line :data="incomeExpenseData" :options="chartDefaults" /></div>
                    </ChartCard>
                    <ChartCard title="Expense Distribution" subtitle="Current month breakdown">
                        <div class="h-64"><Doughnut :data="expenseDistData" :options="doughnutOptions" /></div>
                    </ChartCard>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-4">
                        <h3 class="text-lg font-semibold text-surface-100">Financial Insights</h3>
                        <InsightCard v-for="(insight, i) in insights" :key="i" :type="insight.type" :title="insight.title" :message="insight.message" :value="insight.value" :categories="insight.categories || []" />
                    </div>
                    <div>
                        <FinancialHealthWidget :score="healthScore" />
                    </div>
                </div>
            </section>

            <!-- Section 2: Budget Allocation -->
            <section>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-surface-100 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Budget Allocation Plan
                    </h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <BudgetCard
                        v-for="item in formattedBudgets"
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
            </section>

            <!-- Section 3: Savings Goals Progress -->
            <section>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-surface-100 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                        Savings Goals Progress
                    </h2>
                    <div class="text-surface-300 font-medium bg-surface-800 px-3 py-1 rounded-full border border-surface-700">
                        Total: Rp {{ new Intl.NumberFormat('id-ID').format(stats.total_savings || 0) }}
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <SavingsGoalCard v-for="goal in savings_goals" :key="goal.id" :goal="goal" />
                </div>
            </section>

            <!-- Section 4: Investment Summary -->
            <section>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-surface-100 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        Investment Summary
                    </h2>
                    <div class="text-surface-300 font-medium bg-surface-800 px-3 py-1 rounded-full border border-surface-700">
                        Total: Rp {{ new Intl.NumberFormat('id-ID').format(stats.total_investments || 0) }}
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <InvestmentCard v-for="inv in investments" :key="inv.id" :investment="inv" />
                </div>
            </section>
        </div>
    </AppLayout>
</template>
