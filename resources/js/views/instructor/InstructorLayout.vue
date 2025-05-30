<template>
  <div class="instructor-layout">
    <header>
      <h1>Instructor Panel</h1>
      <!-- Add navigation links here, e.g., to My Courses, Create Course -->
      <nav>
        <router-link to="/instructor/courses">My Courses</router-link> |
        <router-link to="/instructor/courses/create">Create New Course</router-link>
        <!-- Basic logout, or integrate with a main app navigation -->
         | <button @click="handleLogout">Logout</button>
      </nav>
    </header>
    <main class="container mx-auto py-8">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth'; // Adjusted path

const router = useRouter();
const authStore = useAuthStore();

const handleLogout = async () => {
  await authStore.logout();
  router.push({ name: 'login' });
};
</script>

<style scoped>
.instructor-layout header {
  background-color: #4a5568; /* Tailwind gray-700 */
  color: white;
  padding: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.instructor-layout nav a {
  color: white;
  margin-right: 1rem;
  text-decoration: none;
}
.instructor-layout nav a:hover {
  text-decoration: underline;
}
.instructor-layout nav button {
    background-color: #ef4444; /* Tailwind red-500 */
    color: white;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 0.25rem;
    cursor: pointer;
}
.instructor-layout nav button:hover {
    background-color: #dc2626; /* Tailwind red-600 */
}
</style>
