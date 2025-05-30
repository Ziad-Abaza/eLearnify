<template>
    <div class="management-container">
        <div class="management-card">
            <div class="header">
                <h2>User Management</h2>
                <router-link to="/dashboard" class="back-btn">Back to Dashboard</router-link>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td>
                                <span v-for="role in user.roles"
                                      :key="role.id"
                                      class="role-badge"
                                      :class="{ 'admin-role': role.name === 'admin' }">
                                    {{ role.name }}
                                </span>
                            </td>
                            <td>
                                <button @click="editUser(user)" class="action-btn edit">Edit</button>
                                <button @click="deleteUser(user.id)" class="action-btn delete"
                                        :disabled="user.roles.some(r => r.name === 'admin')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Edit User Modal -->
            <div v-if="showEditModal" class="modal">
                <div class="modal-content">
                    <h3>Edit User</h3>
                    <form @submit.prevent="updateUser">
                        <div class="form-group">
                            <label>Name:</label>
                            <input v-model="editingUser.name" required>
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input v-model="editingUser.email" type="email" required>
                        </div>
                        <div class="form-group">
                            <label>Roles:</label>
                            <div class="role-checkboxes">
                                <label v-for="role in availableRoles" :key="role.id" class="checkbox-label">
                                    <input type="checkbox"
                                           v-model="editingUser.selectedRoles"
                                           :value="role.id">
                                    {{ role.name }}
                                </label>
                            </div>
                        </div>
                        <div class="modal-actions">
                            <button type="submit" class="action-btn save">Save</button>
                            <button type="button" @click="showEditModal = false" class="action-btn cancel">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const users = ref([]);
const availableRoles = ref([]);
const showEditModal = ref(false);
const editingUser = ref({
    id: null,
    name: '',
    email: '',
    selectedRoles: []
});

const fetchUsers = async () => {
    try {
        const response = await axios.get('/api/admin/users');
        users.value = response.data;
    } catch (error) {
        console.error('Failed to fetch users:', error);
    }
};

const fetchRoles = async () => {
    try {
        const response = await axios.get('/api/admin/roles');
        availableRoles.value = response.data;
    } catch (error) {
        console.error('Failed to fetch roles:', error);
    }
};

const editUser = (user) => {
    editingUser.value = {
        id: user.id,
        name: user.name,
        email: user.email,
        selectedRoles: user.roles.map(role => role.id)
    };
    showEditModal.value = true;
};

const updateUser = async () => {
    try {
        await axios.put(`/api/admin/users/${editingUser.value.id}`, {
            name: editingUser.value.name,
            email: editingUser.value.email,
            roles: editingUser.value.selectedRoles
        });
        showEditModal.value = false;
        await fetchUsers();
    } catch (error) {
        console.error('Failed to update user:', error);
    }
};

const deleteUser = async (userId) => {
    if (!confirm('Are you sure you want to delete this user?')) return;

    try {
        await axios.delete(`/api/admin/users/${userId}`);
        await fetchUsers();
    } catch (error) {
        console.error('Failed to delete user:', error);
    }
};

onMounted(() => {
    fetchUsers();
    fetchRoles();
});
</script>

<style scoped>
.management-container {
    min-height: 100vh;
    padding: 2rem;
    background: linear-gradient(135deg, #f8fafc 0%, #e0e7ef 100%);
}

.management-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.back-btn {
    padding: 0.5rem 1rem;
    background: #2563eb;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-size: 0.9rem;
}

.table-container {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

th, td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

th {
    background: #f8fafc;
    font-weight: 600;
}

.role-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    margin: 0.25rem;
    border-radius: 12px;
    background: #2563eb;
    color: white;
    font-size: 0.85rem;
}

.role-badge.admin-role {
    background: #7c3aed;
}

.action-btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    margin: 0 0.25rem;
    cursor: pointer;
    font-size: 0.9rem;
    transition: opacity 0.2s;
}

.action-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.action-btn.edit {
    background: #2563eb;
    color: white;
}

.action-btn.delete {
    background: #ef4444;
    color: white;
}

.action-btn.save {
    background: #10b981;
    color: white;
}

.action-btn.cancel {
    background: #6b7280;
    color: white;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    width: 90%;
    max-width: 500px;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
}

.role-checkboxes {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 1.5rem;
}
</style>
