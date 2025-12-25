<template>
  <div class="min-h-screen bg-navy-950">
    <NavBar :title="puzzle?.name || $t('pieces.title')" show-back />

    <div class="max-w-7xl mx-auto px-4 py-6 pb-safe">
      <LoadingSpinner v-if="puzzleStore.loading && !puzzle" />

      <div v-else-if="puzzle" class="space-y-6">
        <div v-if="puzzle.image_url" class="rounded-2xl overflow-hidden flex align-middle justify-center">
          <img :src="puzzle.image_url" :alt="puzzle.name" class="w-auto max-h-64" loading="lazy" />
        </div>

        <div class="grid grid-cols-3 gap-3">
          <PieceCard
            v-for="piece in pieces"
            :key="piece.id"
            :piece="piece"
            :user-state="getPieceState(piece.id)"
            @click="handlePieceClick(piece)"
          />
        </div>

        <div v-if="pieces.length === 0" class="text-center py-12">
          <p class="text-navy-500 text-lg">{{ $t('pieces.no_pieces') }}</p>
        </div>
      </div>
    </div>

    <BottomSheet v-model="showPieceDetails" :title="`Piece #${selectedPiece?.position || ''}`">
      <div v-if="selectedPiece" class="space-y-6">
        <div v-if="selectedPiece.image_url" class="rounded-2xl overflow-hidden">
          <img :src="selectedPiece.image_url" :alt="`Piece ${selectedPiece.position}`" class="w-full h-auto" />
        </div>

        <div class="space-y-3">
          <div class="flex items-center justify-between p-3 bg-navy-800 rounded-xl">
            <span class="text-navy-400">{{ $t('pieces.stars') }}</span>
            <div class="flex items-center gap-1">
              <span class="text-white font-bold">{{ selectedPiece.stars }}</span>
              <span class="text-star-500">★</span>
            </div>
          </div>

          <div v-if="!selectedPiece.is_tradeable" class="p-3 bg-red-600/20 border border-red-600 rounded-xl">
            <p class="text-red-400 text-sm font-bold text-center">
              🔒 {{ $t('pieces.cannot_trade_message') }}
            </p>
          </div>
        </div>

        <div class="space-y-3">
          <h3 class="text-lg font-bold text-white">{{ $t('pieces.your_status') }}</h3>

          <div class="grid grid-cols-3 gap-3">
            <button
              :class="[
                'p-4 rounded-xl border-2 transition-all',
                getPieceState(selectedPiece.id) === 'neutral'
                  ? 'border-navy-600 bg-navy-800 scale-105'
                  : 'border-navy-700 bg-navy-900 opacity-60'
              ]"
              @click="updateState('neutral')"
            >
              <div class="text-center">
                <div class="text-2xl mb-1">⭕</div>
                <div class="text-xs text-navy-400">{{ $t('pieces.neutral') }}</div>
              </div>
            </button>

            <button
              :disabled="!selectedPiece.is_tradeable"
              :class="[
                'p-4 rounded-xl border-2 transition-all',
                getPieceState(selectedPiece.id) === 'need'
                  ? 'border-need-500 bg-need-600/20 scale-105 shadow-lg shadow-need-500/30'
                  : 'border-navy-700 bg-navy-900',
                !selectedPiece.is_tradeable ? 'opacity-30 cursor-not-allowed' : ''
              ]"
              @click="updateState('need')"
            >
              <div class="text-center">
                <div class="text-2xl mb-1">📥</div>
                <div class="text-xs text-need-400">{{ $t('pieces.need') }}</div>
              </div>
            </button>

            <button
              :disabled="!selectedPiece.is_tradeable"
              :class="[
                'p-4 rounded-xl border-2 transition-all',
                getPieceState(selectedPiece.id) === 'have'
                  ? 'border-success-500 bg-success-600/20 scale-105 shadow-lg shadow-success-500/30'
                  : 'border-navy-700 bg-navy-900',
                !selectedPiece.is_tradeable ? 'opacity-30 cursor-not-allowed' : ''
              ]"
              @click="updateState('have')"
            >
              <div class="text-center">
                <div class="text-2xl mb-1">✅</div>
                <div class="text-xs text-success-400">{{ $t('pieces.have') }}</div>
              </div>
            </button>
          </div>
        </div>

        <button
          @click="showPieceDetails = false"
          class="w-full py-3 bg-navy-800 hover:bg-navy-700 text-white font-bold rounded-xl transition-colors"
        >
          {{ $t('pieces.close') }}
        </button>
      </div>
    </BottomSheet>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { usePuzzleStore } from '../stores/puzzleStore';
import { useUserStateStore } from '../stores/userStateStore';
import NavBar from '../components/NavBar.vue';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import PieceCard from '../components/PieceCard.vue';
import BottomSheet from '../components/BottomSheet.vue';

const { t: $t } = useI18n();

const route = useRoute();
const puzzleStore = usePuzzleStore();
const userStateStore = useUserStateStore();

const puzzleId = computed(() => parseInt(route.params.puzzleId));
const puzzle = computed(() => puzzleStore.currentPuzzle);
const pieces = computed(() => puzzle.value?.pieces || []);

const showPieceDetails = ref(false);
const selectedPiece = ref(null);

function getPieceState(pieceId) {
  return userStateStore.getStateForPiece(pieceId);
}

function handlePieceClick(piece) {
  selectedPiece.value = piece;
  showPieceDetails.value = true;
}

async function updateState(newState) {
  if (!selectedPiece.value) return;

  try {
    await userStateStore.updatePieceState(
      selectedPiece.value.id,
      newState,
      selectedPiece.value.is_tradeable
    );
  } catch (error) {
    console.error('Failed to update piece state:', error);
  }
}

onMounted(async () => {
  await puzzleStore.fetchPuzzleDetails(puzzleId.value);
});
</script>

<style scoped>
.pb-safe {
  padding-bottom: env(safe-area-inset-bottom, 1.5rem);
}
</style>
