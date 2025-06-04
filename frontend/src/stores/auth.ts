import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { User } from '@/types';
import { apiService } from '@/dal/api';


export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null);
  const isAuthenticated = ref(false);
  const isLoading = ref(true);

  async function login(email: string, password: string) {
    try {
      await apiService.getCsrfCookie();
      const data = await apiService.loginRequest(email, password);
      user.value = data.user;
      isAuthenticated.value = true;
    } catch (error) {
      user.value = null;
      isAuthenticated.value = false;
      throw error;
    }
  }

  async function logout() {
    try {
      await apiService.logoutRequest();
    } finally {
      user.value = null;
      isAuthenticated.value = false;
    }
  }

  async function checkAuth() {
    isLoading.value = true;
    try {
      const data = await apiService.checkAuthRequest();
      user.value = data;
      isAuthenticated.value = true;
    } catch {
      user.value = null;
      isAuthenticated.value = false;
    } finally {
      isLoading.value = false;
    }
  }

  return { user, isAuthenticated, isLoading, login, logout, checkAuth };
});