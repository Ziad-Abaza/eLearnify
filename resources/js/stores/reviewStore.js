import { defineStore } from 'pinia';
import axios from 'axios';

export const useReviewStore = defineStore('reviewStore', {
  state: () => ({
    reviews: []
  }),
  actions: {
    async fetchReviews() {
      try {
        const response = await axios.get('/api/reviews');
        this.reviews = response.data;
      } catch (error) {
        console.error('Error fetching reviews:', error);
      }
    }
  }
});
