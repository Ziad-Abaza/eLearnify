<template>
  <div class="instructor-course-form px-4 py-10 max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-8">
        <font-awesome-icon :icon="isEditing ? 'fa-solid fa-pen-to-square' : 'fa-solid fa-plus-circle'" class="h-7 w-7 text-blue-600" />
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ isEditing ? 'Edit Course' : 'Create New Course' }}</h2>
    </div>

    <div v-if="loading && isEditing" class="flex flex-col items-center justify-center py-16 bg-white shadow-lg rounded-xl border border-gray-200">
      <font-awesome-icon icon="fa-solid fa-spinner" class="fa-spin h-10 w-10 text-blue-500 mb-3" />
      <span class="text-lg text-gray-500">Loading course details...</span>
    </div>

    <div v-if="initialLoadingError" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6 flex items-center gap-3 shadow-md" role="alert">
      <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="h-6 w-6 text-red-500" />
      <div>
        <p class="font-bold">Error Loading Course</p>
        <p>{{ initialLoadingError }}</p>
      </div>
       <router-link :to="{ name: 'instructor-courses' }" class="ml-auto bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-md text-sm transition-colors">
        Back to Courses
      </router-link>
    </div>

    <form @submit.prevent="handleSubmit" v-if="(!loading && !initialLoadingError) || !isEditing" class="bg-white shadow-xl rounded-2xl p-8 space-y-7 border border-gray-200">
      <div v-if="submitError" class="bg-red-50 border-l-4 border-red-400 text-red-600 p-4 rounded-md mb-2 flex items-center gap-3 shadow-sm" role="alert">
        <font-awesome-icon icon="fa-solid fa-triangle-exclamation" class="h-5 w-5" />
        <span class="font-medium">{{ submitError }}</span>
      </div>

      <div>
        <label for="title" class="block text-gray-700 text-sm font-bold mb-2 flex items-center">
          <font-awesome-icon icon="fa-solid fa-heading" class="mr-2 text-gray-400" />Title
        </label>
        <input type="text" id="title" v-model="course.title" required
               class="block w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800 placeholder-gray-400 shadow-sm text-base"/>
        <p v-if="formErrors.title" class="text-red-500 text-xs mt-1 flex items-center gap-1">
            <font-awesome-icon icon="fa-solid fa-circle-exclamation" /> {{ formErrors.title[0] }}
        </p>
      </div>

      <div>
        <label for="description" class="block text-gray-700 text-sm font-bold mb-2 flex items-center">
          <font-awesome-icon icon="fa-solid fa-align-left" class="mr-2 text-gray-400" />Description
        </label>
        <textarea id="description" v-model="course.description" required rows="5"
                  class="block w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800 placeholder-gray-400 shadow-sm text-base"></textarea>
        <p v-if="formErrors.description" class="text-red-500 text-xs mt-1 flex items-center gap-1">
            <font-awesome-icon icon="fa-solid fa-circle-exclamation" /> {{ formErrors.description[0] }}
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label for="category" class="block text-gray-700 text-sm font-bold mb-2 flex items-center">
            <font-awesome-icon icon="fa-solid fa-folder-tree" class="mr-2 text-gray-400" />Category
          </label>
          <select id="category" v-model="course.category_id" required
                  class="block w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800 bg-white shadow-sm text-base">
            <option disabled value="">Select a category</option>
            <option v-for="category in categories" :key="category.category_id" :value="category.category_id">
              {{ category.name }}
            </option>
          </select>
          <p v-if="formErrors.category_id" class="text-red-500 text-xs mt-1 flex items-center gap-1">
             <font-awesome-icon icon="fa-solid fa-circle-exclamation" /> {{ formErrors.category_id[0] }}
          </p>
        </div>
        <div>
          <label for="price" class="block text-gray-700 text-sm font-bold mb-2 flex items-center">
            <font-awesome-icon icon="fa-solid fa-dollar-sign" class="mr-2 text-gray-400" />Price (USD)
          </label>
          <input type="number" id="price" v-model.number="course.price" min="0" step="0.01"
                 class="block w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800 placeholder-gray-400 shadow-sm text-base"/>
          <p v-if="formErrors.price" class="text-red-500 text-xs mt-1 flex items-center gap-1">
            <font-awesome-icon icon="fa-solid fa-circle-exclamation" /> {{ formErrors.price[0] }}
          </p>
        </div>
      </div>

      <div>
        <label for="thumbnail_url" class="block text-gray-700 text-sm font-bold mb-2 flex items-center">
          <font-awesome-icon icon="fa-solid fa-image" class="mr-2 text-gray-400" />Thumbnail URL (Optional)
        </label>
        <input type="url" id="thumbnail_url" v-model="course.thumbnail_url"
               class="block w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800 placeholder-gray-400 shadow-sm text-base"/>
        <p v-if="formErrors.thumbnail_url" class="text-red-500 text-xs mt-1 flex items-center gap-1">
            <font-awesome-icon icon="fa-solid fa-circle-exclamation" /> {{ formErrors.thumbnail_url[0] }}
        </p>
      </div>

      <div>
        <label class="inline-flex items-center cursor-pointer">
          <input type="checkbox" v-model="course.is_published" class="sr-only peer"/>
          <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
          <span class="ms-3 text-sm font-medium text-gray-700">Published</span>
        </label>
        <p v-if="formErrors.is_published" class="text-red-500 text-xs mt-1 flex items-center gap-1">
            <font-awesome-icon icon="fa-solid fa-circle-exclamation" /> {{ formErrors.is_published[0] }}
        </p>
      </div>

      <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
        <router-link :to="{ name: 'instructor-courses' }"
        class="px-6 py-3 text-sm font-semibold text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
          Cancel
        </router-link>
        <button type="submit"
                :disabled="isSubmitting"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500 to-teal-600 hover:from-green-600 hover:to-teal-700 text-white font-bold py-3 px-6 rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-green-400 disabled:opacity-60 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105">
          <font-awesome-icon :icon="isSubmitting ? 'fa-solid fa-spinner' : 'fa-solid fa-save'" :class="{ 'fa-spin': isSubmitting }" />
          {{ isSubmitting ? 'Saving...' : (isEditing ? 'Update Course' : 'Create Course') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { useAuthStore } from '../../stores/auth';

const props = defineProps({
  courseId: String
});

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const course = ref({
  title: '',
  description: '',
  category_id: '',
  price: 0.00,
  thumbnail_url: '',
  is_published: false
});
const categories = ref([]);
const loading = ref(false);
const initialLoadingError = ref(null);
const isSubmitting = ref(false);
const submitError = ref(null);
const formErrors = ref({});

const isEditing = computed(() => !!props.courseId);

const fetchCategories = async () => {
  try {
    const response = await axios.get('/api/admin/categories', {
         headers: { 'Authorization': `Bearer ${authStore.token}` }
    });
    categories.value = response.data.data;
  } catch (err) {
    console.error('Error fetching categories:', err);
    submitError.value = 'Failed to load categories. Please try reloading.';
  }
};

const fetchCourseDetails = async (id) => {
  loading.value = true;
  initialLoadingError.value = null;
  submitError.value = null;
  formErrors.value = {};
  try {
    const response = await axios.get(`/api/instructor/courses/${id}`, {
      headers: { 'Authorization': `Bearer ${authStore.token}` }
    });
    const fetchedCourse = response.data.data;
    course.value = {
        title: fetchedCourse.title,
        description: fetchedCourse.description,
        category_id: fetchedCourse.category_id,
        price: parseFloat(fetchedCourse.price) || 0.00,
        thumbnail_url: fetchedCourse.thumbnail_url || '',
        is_published: fetchedCourse.is_published || false,
    };
  } catch (err) {
    console.error('Error fetching course details:', err);
    if (err.response?.status === 403 || err.response?.status === 404) {
        initialLoadingError.value = "Course not found or you don't have permission to edit it.";
    } else if (err.response?.status === 401) {
        initialLoadingError.value = 'Your session has expired. Please login again.';
        authStore.logout();
        router.push({ name: 'login' });
    } else {
        initialLoadingError.value = err.response?.data?.message || 'Failed to fetch course details.';
    }
  } finally {
    loading.value = false;
  }
};

const handleSubmit = async () => {
  isSubmitting.value = true;
  submitError.value = null;
  formErrors.value = {};
  const payload = { ...course.value };

  try {
    let response;
    if (isEditing.value) {
      response = await axios.put(`/api/instructor/courses/${props.courseId}`, payload, {
        headers: { 'Authorization': `Bearer ${authStore.token}` }
      });
    } else {
      response = await axios.post('/api/instructor/courses', payload, {
        headers: { 'Authorization': `Bearer ${authStore.token}` }
      });
    }
    router.push({ name: 'instructor-courses' });
  } catch (err) {
    console.error('Error saving course:', err);
    if (err.response?.status === 422) {
        formErrors.value = err.response.data.errors;
        submitError.value = "Please correct the validation errors below.";
    } else if (err.response?.status === 401) {
        submitError.value = 'Your session has expired. Please login again.';
        authStore.logout();
        router.push({ name: 'login' });
    } else {
        submitError.value = err.response?.data?.message || 'An unexpected error occurred while saving the course.';
    }
  } finally {
    isSubmitting.value = false;
  }
};

const resetForm = () => {
    course.value = {
        title: '',
        description: '',
        category_id: '',
        price: 0.00,
        thumbnail_url: '',
        is_published: false
    };
    formErrors.value = {};
    submitError.value = null;
    initialLoadingError.value = null;
}

onMounted(() => {
  fetchCategories();
  if (isEditing.value) {
    fetchCourseDetails(props.courseId);
  } else {
    resetForm();
  }
});

watch(() => props.courseId, (newId, oldId) => {
  if (newId && newId !== oldId) {
    fetchCourseDetails(newId);
  } else if (!newId && oldId) {
    resetForm();
  }
});

watch(() => route.name, (newName) => {
    if (newName === 'instructor-course-create' && isEditing.value) {
        resetForm();
        if (!categories.value.length) fetchCategories();
    }
});

</script>
