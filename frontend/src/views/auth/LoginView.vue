<template>
    <div class="container d-flex align-items-center justify-content-center">
        <div class="login-container card shadow card-body">
            <div class="card-body">
                <h3 class="card-title mb-4 text-center">Login</h3>
                <form @submit.prevent="login">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input v-model="email" type="email" class="form-control" id="email" placeholder="Enter email"
                            required />
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input v-model="password" type="password" class="form-control" id="password"
                            placeholder="Enter password" required />
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-2" :disabled="isLoading">
                        {{ isLoading ? 'Logging in...' : 'Login' }}
                    </button>
                    <div v-if="error" class="alert alert-danger mt-3" role="alert">
                        {{ error }}
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const email = ref('');
const password = ref('');
const error = ref('');
const isLoading = ref(false);
const router = useRouter();
const auth = useAuthStore();

const login = async () => {
    isLoading.value = true;
    error.value = '';
    try {
        await auth.login(email.value, password.value);
        router.replace('/dashboard');
    } catch (e: any) {
        error.value =
            (e && typeof e === 'object' && 'response' in e && e.response?.data?.message)
                ? e.response.data.message
                : (e instanceof Error && e.message)
                    ? e.message
                    : 'Login failed';
    } finally {
        isLoading.value = false;
    }
};
</script>

<style>
body {
    background-color: #f8f9fa;
}

.login-container {
    width: 350px;
}
</style>