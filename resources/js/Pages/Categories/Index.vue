<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import CategoryTable from '@/Components/CategoryTable.vue';
import ModalForm from '@/Components/ModalForm.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({
    categories: Array,
    filters: Object,
});

const showModal = ref(false);
const showDeleteDialog = ref(false);
const editingCategory = ref(null);
const deletingCategory = ref(null);

const form = useForm({
    name: '',
    description: '',
    color: '#3b82f6',
    type: 'expense',
    is_active: true,
});

const activeFilter = ref(props.filters?.type || 'all');

const filteredCategories = computed(() => {
    if (activeFilter.value === 'all') return props.categories;
    return props.categories.filter(c => c.type === activeFilter.value);
});

function openCreate() {
    editingCategory.value = null;
    form.reset();
    form.color = '#3b82f6';
    form.type = 'expense';
    form.is_active = true;
    showModal.value = true;
}

function openEdit(category) {
    editingCategory.value = category;
    form.name = category.name;
    form.description = category.description || '';
    form.color = category.color;
    form.type = category.type;
    form.is_active = category.is_active;
    showModal.value = true;
}

function submit() {
    if (editingCategory.value) {
        form.put(route('categories.update', editingCategory.value.id), {
            onSuccess: () => { showModal.value = false; form.reset(); },
        });
    } else {
        form.post(route('categories.store'), {
            onSuccess: () => { showModal.value = false; form.reset(); },
        });
    }
}

function confirmDelete(category) {
    deletingCategory.value = category;
    showDeleteDialog.value = true;
}

function deleteCategory() {
    router.delete(route('categories.destroy', deletingCategory.value.id), {
        onSuccess: () => { showDeleteDialog.value = false; deletingCategory.value = null; },
    });
}

function toggleCategory(category) {
    router.patch(route('categories.toggle', category.id));
}

const filterTabs = [
    { key: 'all', label: 'All' },
    { key: 'income', label: 'Income' },
    { key: 'expense', label: 'Expense' },
];
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-xl font-bold text-surface-100">Categories</h1>
        </template>

        <PageHeader title="Category Management" subtitle="Manage your income and expense categories.">
            <button
                @click="openCreate"
                class="px-4 py-2 bg-primary-600 hover:bg-primary-500 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Add Category
            </button>
        </PageHeader>

        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 mb-6">
            <button
                v-for="tab in filterTabs"
                :key="tab.key"
                @click="activeFilter = tab.key"
                :class="[
                    'px-4 py-2 text-sm font-medium rounded-lg transition-colors',
                    activeFilter === tab.key
                        ? 'bg-primary-500/15 text-primary-400 border border-primary-500/30'
                        : 'text-surface-400 hover:text-surface-200 border border-surface-700/50 hover:border-surface-600'
                ]"
            >
                {{ tab.label }}
            </button>
        </div>

        <!-- Table -->
        <CategoryTable
            :categories="filteredCategories"
            @edit="openEdit"
            @delete="confirmDelete"
            @toggle="toggleCategory"
        />

        <!-- Create/Edit Modal -->
        <ModalForm
            :show="showModal"
            :title="editingCategory ? 'Edit Category' : 'Create Category'"
            @close="showModal = false"
        >
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-1">Name</label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Category name"
                        required
                    />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-danger-400">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-300 mb-1">Description</label>
                    <textarea
                        v-model="form.description"
                        rows="2"
                        class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Optional description"
                    />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1">Type</label>
                        <select
                            v-model="form.type"
                            class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                        >
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-300 mb-1">Color</label>
                        <div class="flex items-center gap-2">
                            <input
                                v-model="form.color"
                                type="color"
                                class="w-10 h-10 rounded-lg border border-surface-600 cursor-pointer bg-transparent"
                            />
                            <input
                                v-model="form.color"
                                type="text"
                                class="flex-1 bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-surface-200 text-sm focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                                placeholder="#3b82f6"
                            />
                        </div>
                    </div>
                </div>
            </form>

            <template #footer>
                <button
                    @click="showModal = false"
                    class="px-4 py-2 text-sm font-medium text-surface-300 bg-surface-700 hover:bg-surface-600 rounded-lg transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="submit"
                    :disabled="form.processing"
                    class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-500 rounded-lg transition-colors disabled:opacity-50"
                >
                    {{ editingCategory ? 'Update' : 'Create' }}
                </button>
            </template>
        </ModalForm>

        <!-- Delete Confirm -->
        <ConfirmDialog
            :show="showDeleteDialog"
            title="Delete Category"
            :message="`Are you sure you want to delete '${deletingCategory?.name}'? This action cannot be undone.`"
            confirm-text="Delete"
            @confirm="deleteCategory"
            @cancel="showDeleteDialog = false"
        />
    </AppLayout>
</template>
