<template>
  <div class="min-h-screen bg-navy-950">
    <NavBar :title="puzzle?.name || $t('pieces.title', 2)" show-back />

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

          <div class="space-y-3">
            <!-- Need checkbox -->
            <button
              :disabled="!selectedPiece.is_tradeable"
              :class="[
                'w-full p-4 rounded-xl border-2 transition-all text-left',
                currentState.needs
                  ? 'border-need-500 bg-need-600/20 shadow-lg shadow-need-500/30'
                  : 'border-navy-700 bg-navy-900',
                !selectedPiece.is_tradeable ? 'opacity-30 cursor-not-allowed' : ''
              ]"
              @click="toggleNeeds"
            >
              <div class="flex items-center gap-3">
                <div class="text-2xl">📥</div>
                <div class="flex-1">
                  <div class="text-sm font-bold text-white">{{ $t('pieces.need') }}</div>
                  <div class="text-xs text-navy-400 mt-1">{{ $t('pieces.need_description') }}</div>
                </div>
                <div :class="[
                  'w-6 h-6 rounded border-2 flex items-center justify-center',
                  currentState.needs ? 'border-need-500 bg-need-500' : 'border-navy-600'
                ]">
                  <span v-if="currentState.needs" class="text-white text-sm">✓</span>
                </div>
              </div>
            </button>

            <!-- Owns checkbox -->
            <button
              :class="[
                'w-full p-4 rounded-xl border-2 transition-all text-left',
                currentState.owns
                  ? 'border-blue-500 bg-blue-600/20 shadow-lg shadow-blue-500/30'
                  : 'border-navy-700 bg-navy-900'
              ]"
              @click="toggleOwns"
            >
              <div class="flex items-center gap-3">
                <div class="text-2xl">📦</div>
                <div class="flex-1">
                  <div class="text-sm font-bold text-white">{{ $t('pieces.have') }}</div>
                  <div class="text-xs text-navy-400 mt-1">{{ $t('pieces.have_description') }}</div>
                </div>
                <div :class="[
                  'w-6 h-6 rounded border-2 flex items-center justify-center',
                  currentState.owns ? 'border-blue-500 bg-blue-500' : 'border-navy-600'
                ]">
                  <span v-if="currentState.owns" class="text-white text-sm">✓</span>
                </div>
              </div>
            </button>

            <!-- Offers section -->
            <div
              :class="[
                'w-full p-4 rounded-xl border-2 transition-all',
                currentState.offers > 0
                  ? 'border-success-500 bg-success-600/20 shadow-lg shadow-success-500/30'
                  : 'border-navy-700 bg-navy-900'
              ]"
            >
              <button
                :disabled="!selectedPiece.is_tradeable"
                :class="[
                  'w-full text-left',
                  !selectedPiece.is_tradeable ? 'opacity-30 cursor-not-allowed' : ''
                ]"
                @click="toggleOffers"
              >
                <div class="flex items-center gap-3">
                  <div class="text-2xl">✅</div>
                  <div class="flex-1">
                    <div class="text-sm font-bold text-white">{{ $t('pieces.offers') }}</div>
                    <div class="text-xs text-navy-400 mt-1">{{ $t('pieces.offers_description') }}</div>
                  </div>
                  <div :class="[
                    'w-6 h-6 rounded border-2 flex items-center justify-center',
                    currentState.offers > 0 ? 'border-success-500 bg-success-500' : 'border-navy-600'
                  ]">
                    <span v-if="currentState.offers > 0" class="text-white text-sm">✓</span>
                  </div>
                </div>
              </button>

              <!-- Quantity controls -->
              <div v-if="currentState.offers > 0" class="mt-3 pt-3 border-t border-navy-700">
                <div class="flex items-center justify-center gap-4">
                  <button
                    @click="decrementOffers"
                    class="w-10 h-10 rounded-lg bg-navy-800 hover:bg-navy-700 text-white font-bold transition-colors"
                  >
                    −
                  </button>
                  <div class="text-2xl font-bold text-white min-w-[3rem] text-center">
                    {{ currentState.offers }}
                  </div>
                  <button
                    @click="incrementOffers"
                    class="w-10 h-10 rounded-lg bg-navy-800 hover:bg-navy-700 text-white font-bold transition-colors"
                  >
                    +
                  </button>
                </div>
              </div>
            </div>
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

const currentState = computed(() => {
  if (!selectedPiece.value) return { needs: false, owns: false, offers: 0 };
  return userStateStore.getStateForPiece(selectedPiece.value.id);
});

function getPieceState(pieceId) {
  return userStateStore.getStateForPiece(pieceId);
}

function handlePieceClick(piece) {
  selectedPiece.value = piece;
  showPieceDetails.value = true;
}

async function updatePieceData(updates) {
  if (!selectedPiece.value) return;

  try {
    await userStateStore.updatePieceData(
      selectedPiece.value.id,
      updates,
      selectedPiece.value.is_tradeable
    );
  } catch (error) {
    console.error('Failed to update piece data:', error);
  }
}

function toggleNeeds() {
  if (!selectedPiece.value?.is_tradeable) return;
  // Only one state can be active: need, own, or offers
  updatePieceData({
    needs: !currentState.value.needs,
    owns: false,
    offers: 0,
  });
}

function toggleOwns() {
  // Only one state can be active: need, own, or offers
  updatePieceData({
    needs: false,
    owns: !currentState.value.owns,
    offers: 0,
  });
}

function toggleOffers() {
  if (!selectedPiece.value?.is_tradeable) return;
  // Only one state can be active: need, own, or offers
  const newOffers = currentState.value.offers > 0 ? 0 : 1;
  updatePieceData({
    needs: false,
    owns: false,
    offers: newOffers,
  });
}

function incrementOffers() {
  // Only one state can be active: need, own, or offers
  updatePieceData({
    needs: false,
    owns: false,
    offers: currentState.value.offers + 1,
  });
}

function decrementOffers() {
  // Only one state can be active: need, own, or offers
  const newOffers = Math.max(0, currentState.value.offers - 1);
  updatePieceData({
    needs: false,
    owns: false,
    offers: newOffers,
  });
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
