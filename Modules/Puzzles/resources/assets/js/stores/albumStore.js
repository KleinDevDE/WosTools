import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '../utils/api';

export const useAlbumStore = defineStore('albums', () => {
  const albums = ref([]);
  const loading = ref(false);
  const error = ref(null);

  const totalAlbums = computed(() => albums.value.length);

  const getAlbumById = computed(() => {
    return (id) => albums.value.find(album => album.id === parseInt(id));
  });

  async function fetchAlbums() {
    if (albums.value.length > 0) return;

    loading.value = true;
    error.value = null;

    try {
      const { data } = await api.get('/albums');
      albums.value = data.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch albums';
      console.error('Failed to fetch albums:', err);
    } finally {
      loading.value = false;
    }
  }

  async function fetchAlbumDetails(albumId) {
    try {
      const { data } = await api.get(`/albums/${albumId}`);
      const index = albums.value.findIndex(a => a.id === albumId);
      if (index !== -1) {
        albums.value[index] = data.data;
      }
      return data.data;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch album details';
      console.error('Failed to fetch album details:', err);
      throw err;
    }
  }

  function updateAlbumStats(albumId, stats) {
    const album = albums.value.find(a => a.id === albumId);
    if (album) {
      Object.assign(album, stats);
    }
  }

  return {
    albums,
    loading,
    error,
    totalAlbums,
    getAlbumById,
    fetchAlbums,
    fetchAlbumDetails,
    updateAlbumStats,
  };
});
