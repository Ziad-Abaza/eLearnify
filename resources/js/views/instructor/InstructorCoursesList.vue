<template>
  <div class="instructor-courses-list px-4 py-10 max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-4">
      <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
        <font-awesome-icon icon="fa-solid fa-book-open-reader" class="h-8 w-8 text-blue-600" />
        My Courses
      </h2>
      <router-link
        :to="{ name: 'instructor-course-create' }"
        class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg transition-transform transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-400"
      >
        <font-awesome-icon icon="fa-solid fa-plus-circle" class="h-5 w-5" />
        Create New Course
      </router-link>
    </div>

    <div v-if="loading" class="flex flex-col items-center justify-center py-16">
      <font-awesome-icon icon="fa-solid fa-spinner" class="fa-spin h-10 w-10 text-blue-500 mb-3" />
      <span class="text-lg text-gray-500">Loading courses...</span>
    </div>

    <div v-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6 flex items-center gap-3 shadow-md" role="alert">
      <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="h-6 w-6 text-red-500" />
      <div>
        <p class="font-bold">Error</p>
        <p>{{ error }}</p>
      </div>
    </div>

    <div v-if="!loading && courses.length === 0 && !error" class="flex flex-col items-center justify-center py-20 text-center bg-white shadow-lg rounded-xl border border-gray-200">
      <font-awesome-icon icon="fa-solid fa-folder-open" class="h-20 w-20 mb-6 text-blue-300" />
      <p class="text-2xl font-semibold text-gray-700">No Courses Found</p>
      <p class="mt-2 text-gray-500">You haven't created any courses yet. Start by creating your first course!</p>
    </div>

    <div v-if="!loading && courses.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
      <div
        v-for="course in courses"
        :key="course.course_id"
        class="bg-white shadow-xl rounded-2xl overflow-hidden flex flex-col transition-all duration-300 ease-in-out transform hover:scale-[1.03] hover:shadow-2xl border border-gray-200 hover:border-blue-400 group"
      >
        <div class="relative">
          <img v-if="course.thumbnail_url" :src="course.thumbnail_url" alt="Course thumbnail" class="w-full h-52 object-cover group-hover:opacity-90 transition-opacity duration-300"/>
          <div v-else class="w-full h-52 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-400">
            <font-awesome-icon icon="fa-solid fa-image" class="h-12 w-12" />
          </div>
          <span v-if="course.is_published" class="absolute top-3 right-3 bg-green-500 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md flex items-center gap-1">
            <font-awesome-icon icon="fa-solid fa-check-circle" class="h-3 w-3" />
            Published
          </span>
           <span v-else class="absolute top-3 right-3 bg-yellow-500 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md flex items-center gap-1">
            <font-awesome-icon icon="fa-solid fa-hourglass-half" class="h-3 w-3" />
            Draft
          </span>
        </div>
        <div class="p-6 flex-1 flex flex-col">
          <h3 class="text-xl font-bold mb-2 text-gray-800 group-hover:text-blue-700 transition-colors duration-300 line-clamp-2">{{ course.title }}</h3>
          <p class="text-gray-600 text-sm mb-1">
            <font-awesome-icon icon="fa-solid fa-tag" class="mr-1 text-gray-400" />
            Category: <span class="font-semibold text-gray-700">{{ course.category ? course.category.name : 'N/A' }}</span>
          </p>
          <p class="text-gray-600 text-sm mb-3">
            <font-awesome-icon icon="fa-solid fa-video" class="mr-1 text-gray-400" />
            Videos: <span class="font-semibold text-gray-700">{{ course.videos_count }}</span>
          </p>
          <p class="text-gray-700 mb-5 line-clamp-3 flex-1 leading-relaxed">{{ course.description }}</p>
          <div class="flex justify-between items-center mt-auto pt-4 border-t border-gray-100">
            <router-link
              :to="{ name: 'instructor-course-edit', params: { courseId: course.course_id } }"
              class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 font-semibold transition-colors duration-200 px-4 py-2 rounded-md hover:bg-indigo-50"
            >
              <font-awesome-icon icon="fa-solid fa-pencil-alt" />
              Edit
            </router-link>
            <button
              @click="confirmDeleteCourse(course.course_id)"
              class="inline-flex items-center gap-2 text-sm text-red-600 hover:text-red-800 font-semibold transition-colors duration-200 px-4 py-2 rounded-md hover:bg-red-50"
            >
              <font-awesome-icon icon="fa-solid fa-trash-alt" />
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="pagination && pagination.last_page > 1" class="mt-12 flex justify-center items-center gap-3">
      <button
        @click="fetchCourses(pagination.current_page - 1)"
        :disabled="!pagination.prev_page_url"
        class="px-5 py-2 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 font-semibold text-gray-700 flex items-center gap-2"
      >
        <font-awesome-icon icon="fa-solid fa-chevron-left" />
        Previous
      </button>
      <span class="px-5 py-2 text-sm font-medium bg-blue-600 text-white rounded-md shadow">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
      <button
        @click="fetchCourses(pagination.current_page + 1)"
        :disabled="!pagination.next_page_url"
        class="px-5 py-2 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 font-semibold text-gray-700 flex items-center gap-2"
      >
        Next
        <font-awesome-icon icon="fa-solid fa-chevron-right" />
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../../stores/auth';

const courses = ref([]);
const loading = ref(true);
const error = ref(null);
const authStore = useAuthStore();
const pagination = ref(null);

const fetchCourses = async (page = 1) => {
  loading.value = true;
  error.value = null;
  try {
    if (!authStore.token) {
      // This should ideally be handled by a route guard or global error handler
      error.value = 'Not authenticated. Please login again.';
      authStore.logout(); // Consider logging out user
      // router.push({ name: 'login' }); // And redirecting
      return;
    }
    const response = await axios.get(`/api/instructor/courses?page=${page}`, {
      headers: {
        'Authorization': `Bearer ${authStore.token}`
      }
    });
    courses.value = response.data.data;
    pagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        prev_page_url: response.data.prev_page_url,
        next_page_url: response.data.next_page_url,
        total: response.data.total,
    };
  } catch (err) {
    console.error('Error fetching courses:', err);
    if (err.response?.status === 401) {
        error.value = 'Your session has expired. Please login again.';
        authStore.logout();
        // router.push({ name: 'login' });
    } else {
        error.value = err.response?.data?.message || err.message || 'Failed to fetch courses.';
    }
  } finally {
    loading.value = false;
  }
};

const confirmDeleteCourse = async (courseId) => {
  if (window.confirm('Are you sure you want to delete this course? This action cannot be undone and will remove all associated videos and quiz data.')) {
    try {
      await axios.delete(`/api/instructor/courses/${courseId}`, {
        headers: {
          'Authorization': `Bearer ${authStore.token}`
        }
      });
      // Refresh the current page after deletion
      fetchCourses(pagination.value?.current_page || 1);
    } catch (err) {
      console.error('Error deleting course:', err);
      error.value = err.response?.data?.message || 'Failed to delete course.';
    }
  }
};

onMounted(() => {
  fetchCourses();
});
</script>
