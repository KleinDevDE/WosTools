import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '../utils/api';
import { useUserStateStore } from './userStateStore';

export const useMatchStore = defineStore('matches', () => {
  const canGetFrom = ref([]);
  const canHelpWith = ref([]);
  const loading = ref(false);
  const error = ref(null);
  const lastFetch = ref(null);

  const totalMatches = computed(() => canGetFrom.value.length + canHelpWith.value.length);

  async function fetchMatches(forceRefresh = false) {
    const userStateStore = useUserStateStore();

    // Ensure user states are loaded first
    if (userStateStore.states.size === 0) {
      await userStateStore.fetchUserStates();
    }

    if (!forceRefresh && canGetFrom.value.length > 0 && lastFetch.value) {
      const timeSinceLastFetch = Date.now() - lastFetch.value;
      if (timeSinceLastFetch < 30000) {
        return;
      }
    }

    if (userStateStore.needPieces.length === 0 && userStateStore.havePieces.length === 0) {
      canGetFrom.value = [];
      canHelpWith.value = [];
      lastFetch.value = Date.now();
      return;
    }

    loading.value = true;
    error.value = null;

    try {
      const { data } = await api.get('/matches');
      canGetFrom.value = data.data.can_get_from || [];
      canHelpWith.value = data.data.can_help_with || [];
      lastFetch.value = Date.now();
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch matches';
      console.error('Failed to fetch matches:', err);
    } finally {
      loading.value = false;
    }
  }

  function clearMatches() {
    canGetFrom.value = [];
    canHelpWith.value = [];
    lastFetch.value = null;
  }

  return {
    canGetFrom,
    canHelpWith,
    loading,
    error,
    totalMatches,
    lastFetch,
    fetchMatches,
    clearMatches,
  };
});
