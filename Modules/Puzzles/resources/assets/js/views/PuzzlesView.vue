<template>
  <div class="min-h-screen bg-navy-950">
    <NavBar :title="album?.name || 'Puzzles'" show-back />

    <div class="max-w-7xl mx-auto px-4 py-6 pb-safe">
      <LoadingSpinner v-if="puzzleStore.loading && puzzles.length === 0" />

      <div v-else class="space-y-4">
        <router-link
          v-for="puzzle in puzzles"
          :key="puzzle.id"
          :to="`/puzzles/puzzles/${puzzle.id}/pieces`"
          class="block bg-navy-900 rounded-2xl overflow-hidden border border-navy-700 hover:border-glow-500 transition-all active:scale-98"
        >
          <div class="flex items-center p-4">
            <div v-if="puzzle.image_url" class="flex-shrink-0 w-20 h-20 rounded-xl overflow-hidden">
              <img :src="puzzle.image_url" :alt="puzzle.name" class="w-full h-full object-cover" loading="lazy" />
            </div>
            <div v-else class="flex-shrink-0 w-20 h-20 rounded-xl bg-navy-800 flex items-center justify-center">
              <svg class="w-10 h-10 text-navy-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" />
              </svg>
            </div>

            <div class="flex-1 ml-4 min-w-0">
              <h3 class="text-lg font-bold text-white truncate">{{ puzzle.name }}</h3>
              <p class="text-sm text-navy-400 mt-1">{{ puzzle.pieces_count || 0 }} pieces</p>

              <div v-if="puzzle.pieces_count > 0" class="mt-3">
                <div class="flex items-center justify-between text-xs mb-1">
                  <span class="text-navy-400">Collected</span>
                  <span class="text-success-400 font-bold">{{ puzzle.completed_pieces || 0 }}/{{ puzzle.pieces_count }}</span>
                </div>
                <div class="h-2 bg-navy-800 rounded-full overflow-hidden">
                  <div
                    class="h-full bg-gradient-to-r from-success-600 to-success-400 rounded-full transition-all duration-500"
                    :style="{ width: `${puzzle.completion_percentage || 0}%` }"
                  />
                </div>
              </div>
            </div>

            <svg class="flex-shrink-0 w-6 h-6 text-navy-600 ml-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </div>
        </router-link>

        <div v-if="puzzles.length === 0 && !puzzleStore.loading" class="text-center py-12">
          <p class="text-navy-500 text-lg">No puzzles found in this album</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { useAlbumStore } from '../stores/albumStore';
import { usePuzzleStore } from '../stores/puzzleStore';
import NavBar from '../components/NavBar.vue';
import LoadingSpinner from '../components/LoadingSpinner.vue';

const route = useRoute();
const albumStore = useAlbumStore();
const puzzleStore = usePuzzleStore();

const albumId = computed(() => parseInt(route.params.albumId));
const album = computed(() => albumStore.getAlbumById(albumId.value));
const puzzles = computed(() => puzzleStore.getPuzzlesByAlbumId(albumId.value));

onMounted(async () => {
  await puzzleStore.fetchPuzzles(albumId.value);
});
</script>

<style scoped>
.active\:scale-98:active {
  transform: scale(0.98);
}

.pb-safe {
  padding-bottom: env(safe-area-inset-bottom, 1.5rem);
}
</style>
