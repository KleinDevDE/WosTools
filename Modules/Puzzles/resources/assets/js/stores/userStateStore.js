import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '../utils/api';
import { useMatchStore } from './matchStore';

export const useUserStateStore = defineStore('userStates', () => {
  const states = ref(new Map());
  const loading = ref(false);
  const error = ref(null);

  const getStateForPiece = computed(() => {
    return (pieceId) => states.value.get(pieceId) || 'neutral';
  });

  const needPieces = computed(() => {
    return Array.from(states.value.entries())
      .filter(([_, state]) => state === 'need')
      .map(([pieceId]) => pieceId);
  });

  const havePieces = computed(() => {
    return Array.from(states.value.entries())
      .filter(([_, state]) => state === 'have')
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
        stateMap.set(item.piece_id, item.state);
      });
      states.value = stateMap;
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch user states';
      console.error('Failed to fetch user states:', err);
    } finally {
      loading.value = false;
    }
  }

  async function updatePieceState(pieceId, newState, isTradeable = true) {
    if (!isTradeable && newState !== 'neutral') {
      throw new Error('Cannot trade 5★ pieces');
    }

    const previousState = states.value.get(pieceId) || 'neutral';

    states.value.set(pieceId, newState);

    try {
      await api.post(`/pieces/${pieceId}/state`, { state: newState });

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

  function cycleState(pieceId, isTradeable = true) {
    if (!isTradeable) return 'neutral';

    const current = getStateForPiece.value(pieceId);
    const stateOrder = ['neutral', 'need', 'have'];
    const currentIndex = stateOrder.indexOf(current);
    const nextIndex = (currentIndex + 1) % stateOrder.length;
    return stateOrder[nextIndex];
  }

  return {
    states,
    loading,
    error,
    getStateForPiece,
    needPieces,
    havePieces,
    fetchUserStates,
    updatePieceState,
    cycleState,
  };
});
