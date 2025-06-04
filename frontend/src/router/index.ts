import { createRouter, createWebHistory } from 'vue-router';
import type { RouteRecordRaw } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const Login = () => import('@/views/auth/LoginView.vue');
const Dashboard = () => import('@/views/DashboardView.vue');
const Transactions = () => import('@/views/TransactionsView.vue');

const routes: RouteRecordRaw[] = [
    { path: '/', redirect: '/dashboard' },
    {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: { requiresGuest: true },
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: Dashboard,
        meta: { requiresAuth: true },
    },
    {
        path: '/transactions',
        name: 'Transactions',
        component: Transactions,
        meta: { requiresAuth: true },
    },
    { path: '/:pathMatch(.*)*', redirect: '/dashboard' },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to) => {
    const auth = useAuthStore();
    console.log(auth.isLoading, auth.isAuthenticated);

    if (auth.isLoading) {
        await auth.checkAuth();
    }

    if (to.name === 'Login' && auth.isAuthenticated) {
        return { name: 'Dashboard' };
    }

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        return { name: 'Login', query: { redirect: to.fullPath } };
    }

    if (to.meta.requiresGuest && auth.isAuthenticated) {
        return { name: 'Dashboard' };
    }

    return true;
});


export default router;
