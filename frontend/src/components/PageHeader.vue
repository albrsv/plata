<template>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h4 class="mb-0">{{ title }}</h4>
        <button @click="handleLogout" class="btn btn-link">
            Logout
        </button>
    </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

defineProps<{
    title: string;
}>();

const auth = useAuthStore();
const router = useRouter();

const handleLogout = async () => {
    await auth.logout();
    router.replace({ name: 'Login' });
};
</script>