<template>
    <div class="management-container">
        <div class="management-card">
            <div class="header">
                <h2>Role Management</h2>
                <div class="header-actions">
                    <button @click="showCreateModal = true" class="action-btn create">Create Role</button>
                    <router-link to="/dashboard" class="back-btn">Back to Dashboard</router-link>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Users Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="role in roles" :key="role.id">
                            <td>
                                <span class="role-badge" :class="{ 'admin-role': role.name === 'admin' }">
                                    {{ role.name }}
                                </span>
                            </td>
                            <td>{{ role.description }}</td>
                            <td>{{ role.users_count }}</td>
                            <td>
                                <button @click="editRole(role)" class="action-btn edit">Edit</button>
                                <button @click="deleteRole(role.id)"
                                        class="action-btn delete"
                                        :disabled="role.name === 'admin' || role.name === 'user'">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Create/Edit Role Modal -->
            <div v-if="showCreateModal || showEditModal" class="modal">
                <div class="modal-content">
                    <h3>{{ showEditModal ? 'Edit Role' : 'Create Role' }}</h3>
                    <form @submit.prevent="showEditModal ? updateRole() : createRole()">
                        <div class="form-group">
                            <label>Name:</label>
                            <input v-model="roleForm.name" required
                                   :disabled="showEditModal && (roleForm.name === 'admin' || roleForm.name === 'user')">
                        </div>
                        <div class="form-group">
                            <label>Description:</label>
                            <textarea v-model="roleForm.description" required></textarea>
                        </div>
                        <div class="modal-actions">
                            <button type="submit" class="action-btn save">
                                {{ showEditModal ? 'Update' : 'Create' }}
                            </button>
                            <button type="button" @click="closeModal" class="action-btn cancel">Cancel</button>
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

const roles = ref([]);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const roleForm = ref({
    id: null,
    name: '',
    description: ''
});

const fetchRoles = async () => {
    try {
        const response = await axios.get('/api/admin/roles');
        roles.value = response.data;
    } catch (error) {
        console.error('Failed to fetch roles:', error);
    }
};

const createRole = async () => {
    try {
        await axios.post('/api/admin/roles', roleForm.value);
        closeModal();
        await fetchRoles();
    } catch (error) {
        console.error('Failed to create role:', error);
    }
};

const editRole = (role) => {
    roleForm.value = { ...role };
    showEditModal.value = true;
};

const updateRole = async () => {
    try {
        await axios.put(`/api/admin/roles/${roleForm.value.id}`, roleForm.value);
        closeModal();
        await fetchRoles();
    } catch (error) {
        console.error('Failed to update role:', error);
    }
};

const deleteRole = async (roleId) => {
    if (!confirm('Are you sure you want to delete this role?')) return;

    try {
        await axios.delete(`/api/admin/roles/${roleId}`);
        await fetchRoles();
    } catch (error) {
        console.error('Failed to delete role:', error);
    }
};

const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    roleForm.value = {
        id: null,
        name: '',
        description: ''
    };
};

onMounted(() => {
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

.header-actions {
    display: flex;
    gap: 1rem;
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

.action-btn.create {
    background: #10b981;
    color: white;
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
    background: #6b7280; /* Gray */
    color: white;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    min-width: 300px;
    max-width: 500px;
}

.modal-content h3 {
    margin-top: 0;
    margin-bottom: 1.5rem;
}

.modal-actions {
    margin-top: 1.5rem;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
}

/* Ensure other styles are in place if needed for responsiveness or specific elements */

</style>
