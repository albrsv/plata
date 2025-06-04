import axios, { type AxiosInstance } from 'axios';
import type { Transaction, Balance, User } from '@/dal/models';

class ApiService {
    private api: AxiosInstance;

    constructor() {
        this.api = axios.create({
            baseURL: import.meta.env.VITE_API_BASE_URL,
            withCredentials: true,
            withXSRFToken: true,
            headers: {
                Accept: 'application/json',
            },
        });
    }

    async fetchTransactions(params: { search?: string; sort?: string }): Promise<Transaction[]> {
        const response = await this.api.get<{ data: Transaction[] }>('/api/transactions', { params });
        return response.data.data;
    }

    async fetchBalances(): Promise<Balance[]> {
        const response = await this.api.get<{ data: Balance[] }>('/api/balances');
        return response.data.data;
    }

    async fetchRecentTransactions(): Promise<Transaction[]> {
        const response = await this.api.get<{ data: Transaction[] }>('/api/transactions/recent');
        return response.data.data;
    }

    async getCsrfCookie() {
        await this.api.get('/sanctum/csrf-cookie');
    }

    async loginRequest(email: string, password: string) {
        const response = await this.api.post('/api/login', { email, password });
        return response.data;
    }

    async logoutRequest() {
        await this.api.post('/api/logout');
    }

    async checkAuthRequest(): Promise<User> {
        const response = await this.api.get('/api/user');
        return response.data;
    }
}

export const apiService = new ApiService();