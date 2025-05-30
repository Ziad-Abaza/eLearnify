<template>
  <div class="relative flex flex-col min-h-screen bg-white" style='font-family: Lexend, "Noto Sans", sans-serif;'>
    <main class="flex flex-col items-center px-10 py-8">
      <section
        class="flex flex-col items-center justify-center w-full max-w-4xl p-8 text-center bg-cover bg-center rounded-lg"
        style='background-image: linear-gradient(rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.4) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuDEPZl9otSh5i9xvJl8V-mI9i-XNUZENr_UTuVfWaXOuMB3gGTTc5oCbNwofiOAsBk0Y8mGiWmFQbGNQziofNuvEI8NGyFqFdA7Q7ldRdLi6C1eIeu9CehkOXP5jje5JCdKqX0wtPnD-Ukcp_K370FNGr0VylaNAudrvYKefRQNUf74Zzzy34CGxUw1-IvFFzG2BKI9zGjtRCpfNrFYVhDGTzeTjMa5yenDg8J8YI4tBm7hV_jAxr2tXNSJKWJArENNL6XYXUOcWR0");'
      >
        <h1 class="text-4xl font-extrabold text-white">Unlock Your Potential with EduStream</h1>
        <p class="mt-4 text-sm text-white">Explore a wide range of courses taught by industry experts. Learn at your own pace and achieve your goals.</p>
        <button class="px-6 py-3 mt-6 text-sm font-bold text-white bg-blue-600 rounded-full">Browse Courses</button>
      </section>

      <section class="w-full max-w-4xl mt-12">
        <h2 class="text-2xl font-bold text-gray-900">Featured Courses</h2>
        <div class="grid grid-cols-1 gap-6 mt-6 sm:grid-cols-2 lg:grid-cols-3">
          <div v-for="course in courses" :key="course.id" class="p-4 bg-white border rounded-lg">
            <div
              class="w-full h-40 bg-center bg-cover rounded-lg"
              :style="{ backgroundImage: `url(${course.image})` }"
            ></div>
            <h3 class="mt-4 text-lg font-medium text-gray-900">{{ course.title }}</h3>
            <p class="mt-2 text-sm text-gray-600">{{ course.description }}</p>
          </div>
        </div>
      </section>

      <section class="w-full max-w-4xl mt-12">
        <h2 class="text-2xl font-bold text-gray-900">What Our Students Say</h2>
        <div class="flex flex-col gap-6 mt-6">
          <div v-for="review in reviews" :key="review.id" class="p-4 bg-white border rounded-lg">
            <div class="flex items-center gap-4">
              <div
                class="w-12 h-12 bg-center bg-cover rounded-full"
                :style="{ backgroundImage: `url(${review.avatar})` }"
              ></div>
              <div>
                <h3 class="text-lg font-medium text-gray-900">{{ review.name }}</h3>
                <p class="text-sm text-gray-600">{{ review.date }}</p>
              </div>
            </div>
            <p class="mt-4 text-sm text-gray-800">{{ review.comment }}</p>
          </div>
        </div>
      </section>
    </main>

    <footer class="flex flex-col items-center justify-center w-full px-10 py-8 mt-12 bg-gray-100">
      <h2 class="text-xl font-bold text-gray-900">Ready to Start Learning?</h2>
      <button class="px-6 py-3 mt-4 text-sm font-bold text-white bg-blue-600 rounded-full">Explore Courses</button>
      <nav class="flex items-center gap-6 mt-6 text-sm text-gray-600">
        <a href="#">Home</a>
        <a href="#">Courses</a>
        <a href="#">About Us</a>
        <a href="#">Contact</a>
      </nav>
      <p class="mt-6 text-xs text-gray-500">© 2024 EduStream. All rights reserved.</p>
    </footer>
  </div>
</template>

<script setup>
import { useCourseStore } from '@/stores/courseStore';
import { useReviewStore } from '@/stores/reviewStore';

const courseStore = useCourseStore();
const reviewStore = useReviewStore();

const courses = courseStore.courses;
const reviews = reviewStore.reviews;

courseStore.fetchCourses();
reviewStore.fetchReviews();
</script>

<style scoped>
/* Add any additional styles here */
</style>