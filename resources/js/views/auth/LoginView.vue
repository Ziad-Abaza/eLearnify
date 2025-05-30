<template>
    <div class="login-container">
        <div class="login-card">
            <h2>Login</h2>
            <form @submit.prevent="handleLogin">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" v-model="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" v-model="password" required>
                </div>
                <div v-if="error" class="error">{{ error }}</div>
                <button type="submit" class="login-btn" :disabled="loading">
                    {{ loading ? 'Logging in...' : 'Login' }}
                </button>
            </form>
            <div class="switch-link">
                <router-link to="/register">Don't have an account? Register</router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const password = ref('');
const error = ref('');
const loading = ref(false);

const handleLogin = async () => {
    try {
        loading.value = true;
        error.value = '';
        await authStore.login({
            email: email.value,
            password: password.value
        });
        router.push('/dashboard');
    } catch (err) {
        error.value = err.response?.data?.message || 'Login failed. Please try again.';
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f8fafc 0%, #e0e7ef 100%);
}

.login-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    padding: 40px 32px 32px 32px;
    min-width: 350px;
    max-width: 400px;
    text-align: center;
}

h2 {
    margin-bottom: 20px;
    color: #2563eb;
}

.form-group {
    margin-bottom: 16px;
    text-align: left;
}

label {
    display: block;
    margin-bottom: 6px;
    color: #374151;
    font-weight: 500;
}

input {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 1rem;
    transition: border 0.2s;
}
input:focus {
    border-color: #2563eb;
    outline: none;
}

.error {
    color: #ef4444;
    margin-bottom: 12px;
    font-size: 0.98rem;
}

.login-btn {
    width: 100%;
    padding: 10px 0;
    background: #2563eb;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: 8px;
    transition: background 0.2s;
}
.login-btn:disabled {
    background: #93c5fd;
    cursor: not-allowed;
}
.login-btn:hover:not(:disabled) {
    background: #1d4ed8;
}

.switch-link {
    margin-top: 18px;
    font-size: 0.98rem;
}
.switch-link a {
    color: #2563eb;
    text-decoration: none;
    font-weight: 500;
}
.switch-link a:hover {
    text-decoration: underline;
}
</style>
