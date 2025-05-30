<template>
    <div class="dashboard-container">
        <div class="dashboard-card">
            <h1>Dashboard</h1>
            <div v-if="user">
                <p class="welcome">Welcome, <span class="username">{{ user.name }}</span>!</p>
                <div v-if="user.roles && user.roles.length" class="roles-section">
                    <h3>Your Roles:</h3>
                    <ul class="roles-list">
                        <li v-for="role in user.roles" :key="role.id" class="role-item">
                            <span class="role-badge" :class="{ 'admin-role': role.name === 'admin' }">
                                {{ role.name }}
                            </span>
                        </li>
                    </ul>
                </div>

                <!-- Admin-only section -->
                <div v-if="isAdmin" class="admin-section">
                    <h3>Admin Panel</h3>
                    <p>You have access to administrative features.</p>
                    <div class="admin-actions">
                        <router-link to="/admin/users" class="action-btn">Manage Users</router-link>
                        <router-link to="/admin/roles" class="action-btn">Manage Roles</router-link>
                    </div>
                </div>
            </div>
            <button class="logout-btn" @click="handleLogout">Logout</button>
        </div>
    </div>
</template>

<script setup>
import { onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const user = computed(() => authStore.getUser);
const isAdmin = computed(() => authStore.isAdmin);

onMounted(async () => {
    try {
        await authStore.fetchUser();
    } catch (error) {
        console.error('Failed to fetch user:', error);
        router.push('/login');
    }
});

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};
</script>

<style scoped>
.dashboard-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f8fafc 0%, #e0e7ef 100%);
}

.dashboard-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    padding: 40px 32px 32px 32px;
    min-width: 350px;
    max-width: 500px;
    text-align: center;
}

h1 {
    margin-bottom: 16px;
    color: #2d3748;
}

.welcome {
    font-size: 1.1rem;
    margin-bottom: 12px;
}

.username {
    font-weight: bold;
    color: #2563eb;
}

.roles-section {
    margin: 24px 0;
}

.roles-list {
    list-style: none;
    padding: 0;
    margin: 16px 0 0 0;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
}

.role-item {
    margin: 0;
}

.role-badge {
    background: #2563eb;
    color: #fff;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.95rem;
    font-weight: 500;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(37,99,235,0.08);
    text-transform: capitalize;
}

.role-badge.admin-role {
    background: #7c3aed;
}

.admin-section {
    margin: 32px 0;
    padding: 24px;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.admin-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    margin-top: 16px;
}

.action-btn {
    padding: 8px 16px;
    background: #2563eb;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
}

.action-btn:hover {
    background: #1d4ed8;
}

.logout-btn {
    margin-top: 32px;
    padding: 10px 24px;
    background: #ef4444;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.logout-btn:hover {
    background: #dc2626;
}
</style>
