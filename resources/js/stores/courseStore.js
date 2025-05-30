import { defineStore } from 'pinia';
import axios from 'axios';

export const useCourseStore = defineStore('courseStore', {
  state: () => ({
    courses: []
  }),
  actions: {
    async fetchCourses() {
      try {
        const response = await axios.get('/api/courses');
        this.courses = response.data;
      } catch (error) {
        console.error('Error fetching courses:', error);
      }
    }
  }
});
