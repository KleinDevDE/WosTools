import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '../utils/api';

export const usePuzzleStore = defineStore('puzzles', () => {
  const puzzlesByAlbum = ref({});
  const currentPuzzle = ref(null);
  const loading = ref(false);
  const error = ref(null);

  const getPuzzlesByAlbumId = computed(() => {
    return (albumId) => puzzlesByAlbum.value[albumId] || [];
  });

  async function fetchPuzzles(albumId) {
    if (puzzlesByAlbum.value[albumId]?.length > 0) {
      return puzzlesByAlbum.value[albumId];
    }

    loading.value = true;
    error.value = null;

    try {
      const { data } = await api.get(`/albums/${albumId}/puzzles`);
      puzzlesByAlbum.value[albumId] = data.data;
      return data.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch puzzles';
      console.error('Failed to fetch puzzles:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function fetchPuzzleDetails(puzzleId) {
    loading.value = true;
    error.value = null;

    try {
      const { data } = await api.get(`/puzzles/${puzzleId}`);
      currentPuzzle.value = data.data;
      return data.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch puzzle details';
      console.error('Failed to fetch puzzle details:', err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  function clearCurrentPuzzle() {
    currentPuzzle.value = null;
  }

  return {
    puzzlesByAlbum,
    currentPuzzle,
    loading,
    error,
    getPuzzlesByAlbumId,
    fetchPuzzles,
    fetchPuzzleDetails,
    clearCurrentPuzzle,
  };
});
