import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '../utils/api';
import { useMatchStore } from './matchStore';

export const useUserStateStore = defineStore('userStates', () => {
  const states = ref(new Map());
  const loading = ref(false);
  const error = ref(null);

  const getStateForPiece = computed(() => {
    return (pieceId) => states.value.get(pieceId) || { needs: false, owns: false, offers: 0 };
  });

  const needPieces = computed(() => {
    return Array.from(states.value.entries())
      .filter(([_, state]) => state.needs === true)
      .map(([pieceId]) => pieceId);
  });

  const ownedPieces = computed(() => {
    return Array.from(states.value.entries())
      .filter(([_, state]) => state.owns === true)
      .map(([pieceId]) => pieceId);
  });

  const offeringPieces = computed(() => {
    return Array.from(states.value.entries())
      .filter(([_, state]) => state.offers > 0)
      .map(([pieceId]) => pieceId);
  });

  async function fetchUserStates() {
    if (states.value.size > 0) return;

    loading.value = true;
    error.value = null;

    try {
      const { data } = await api.get('/user/states');
      const stateMap = new Map();
      data.data.forEach(item => {
        stateMap.set(item.piece_id, {
          needs: item.needs,
          owns: item.owns,
          offers: item.offers,
        });
      });
      states.value = stateMap;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch user states';
      console.error('Failed to fetch user states:', err);
    } finally {
      loading.value = false;
    }
  }

  async function updatePieceData(pieceId, { needs, owns, offers }, isTradeable = true) {
    if (!isTradeable && (needs || offers > 0)) {
      throw new Error('Cannot trade 5★ pieces');
    }

    const previousState = states.value.get(pieceId) || { needs: false, owns: false, offers: 0 };
    const newState = { needs, owns, offers };

    states.value.set(pieceId, newState);

    try {
      await api.post(`/pieces/${pieceId}/state`, { needs, owns, offers });

      // Refresh matches after state change if it affects tradeable pieces
      if (isTradeable) {
        const matchStore = useMatchStore();
        await matchStore.fetchMatches(true);
      }
    } catch (err) {
      states.value.set(pieceId, previousState);
      error.value = err.response?.data?.message || 'Failed to update piece state';
      console.error('Failed to update piece state:', err);
      throw err;
    }
  }

  return {
    states,
    loading,
    error,
    getStateForPiece,
    needPieces,
    ownedPieces,
    offeringPieces,
    fetchUserStates,
    updatePieceData,
  };
});
