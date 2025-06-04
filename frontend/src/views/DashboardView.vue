<template>
    <div class="container-fluid px-4 py-4">
        <PageHeader title="Dashboard" />
        <div class="content-width mx-auto">
            <!-- Balances Section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Balances</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Currency</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="balance in balances" :key="balance.id">
                                    <td>{{ balance.currency }}</td>
                                    <td>{{ balance.amount }}</td>
                                    <td>{{ balance.status }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Transactions</h5>
                    <router-link to="/transactions" class="btn btn-sm btn-outline-primary">
                        View All
                    </router-link>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Comment</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="transaction in recentTransactions" :key="transaction.id">
                                    <td>{{ transaction.type }} </td>
                                    <td>{{ transaction.amount }}</td>
                                    <td>{{ transaction.status }}</td>
                                    <td>{{ transaction.comment }}</td>
                                    <td>{{ formatDate(transaction.created_at) }}</td>
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
import { ref, onMounted, onUnmounted } from 'vue';
import { apiService } from '@/dal/api';
import { formatDate } from '@/utils/dateFormatter';
import PageHeader from '@/components/PageHeader.vue';
import type { Balance, Transaction } from '@/dal/models';

const balances = ref<Balance[]>([]);
const recentTransactions = ref<Transaction[]>([]);

let refreshInterval: number;

const fetchData = async () => {
    [balances.value, recentTransactions.value] = await Promise.all([
        apiService.fetchBalances(),
        apiService.fetchRecentTransactions()
    ]);
};

onMounted(() => {
    fetchData();

    // Set up auto-refresh every 5 seconds
    refreshInterval = setInterval(fetchData, 5000);
});

onUnmounted(() => {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
</script>

<style scoped>
.content-width {
    width: 1000px;
    max-width: 100%;
}

.table {
    margin-bottom: 0;
}

.badge {
    font-size: 0.8em;
    padding: 0.4em 0.6em;
}
</style>