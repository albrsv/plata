<template>
    <div class="container-fluid px-4 py-4">
        <div class="content-width mx-auto">
            <PageHeader title="Transactions" />

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <h5 class="mb-0">All Transactions</h5>
                        <input type="text" v-model="searchQuery" class="form-control form-control-sm w-auto"
                            placeholder="Search by comment..." @input="handleSearch" />
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>
                                        <button class="btn btn-link text-dark p-0 text-decoration-none"
                                            @click="toggleSort">
                                            Date
                                            <i class="bi" :class="{
                                                'bi-sort-down': sortOrder === 'desc',
                                                'bi-sort-up': sortOrder === 'asc'
                                            }"></i>
                                        </button>
                                    </th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="transaction in transactions" :key="transaction.id">
                                    <td>{{ formatDate(transaction.created_at) }}</td>
                                    <td>{{ transaction.type }}</td>
                                    <td>{{ transaction.amount }}</td>
                                    <td>{{ transaction.status }}</td>
                                    <td>{{ transaction.comment }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import PageHeader from '@/components/PageHeader.vue';
import { apiService } from '@/dal/api';
import { formatDate } from '@/utils/dateFormatter';
import debounce from 'lodash/debounce';
import type { Transaction } from '@/dal/models';

const transactions = ref<Transaction[]>([]);
const searchQuery = ref('');
const sortOrder = ref<'asc' | 'desc'>('desc');

const fetchTransactions = async () => {
    try {
        transactions.value = await apiService.fetchTransactions({
            search: searchQuery.value,
            sort: sortOrder.value === 'desc' ? '-created_at' : 'created_at'
        });
    } catch (error) {
        console.error('Failed to fetch transactions:', error);
    }
};

const handleSearch = debounce(() => {
    fetchTransactions();
}, 300);

const toggleSort = () => {
    sortOrder.value = sortOrder.value === 'desc' ? 'asc' : 'desc';
    fetchTransactions();
};

onMounted(() => {
    fetchTransactions();
});
</script>

<style scoped>
.content-width {
    width: 1000px;
    max-width: 100%;
}

.badge {
    font-size: 0.8em;
    padding: 0.4em 0.6em;
}

.bi {
    font-size: 0.8em;
    margin-left: 0.25rem;
}
</style>