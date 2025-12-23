<template>
  <div class="min-h-screen bg-navy-950">
    <NavBar :title="$t('search.title')" show-back />

    <div class="max-w-7xl mx-auto px-4 py-6 pb-safe">
      <div class="mb-6">
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="$t('search.placeholder')"
            class="w-full px-4 py-3 pl-12 bg-navy-900 border border-navy-700 rounded-xl text-white placeholder-navy-500 focus:outline-none focus:border-glow-500 transition-colors"
            @input="handleSearch"
          />
          <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-navy-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>

      <LoadingSpinner v-if="searching" />

      <div v-else-if="searchQuery" class="space-y-8">
        <section v-if="albums.length > 0">
          <h2 class="text-lg font-bold text-white mb-4">{{ $t('search.albums') }}</h2>
          <div class="space-y-3">
            <router-link
              v-for="album in albums"
              :key="album.id"
              :to="`/puzzles/albums/${album.id}/puzzles`"
              class="block bg-navy-900 rounded-xl p-4 border border-navy-700 hover:border-glow-500 transition-all active:scale-98"
            >
              <h3 class="text-white font-bold">{{ album.name }}</h3>
              <p class="text-navy-400 text-sm mt-1">{{ album.puzzles_count || 0 }} {{ $t('albums.puzzles') }}</p>
            </router-link>
          </div>
        </section>

        <section v-if="puzzles.length > 0">
          <h2 class="text-lg font-bold text-white mb-4">{{ $t('search.puzzles') }}</h2>
          <div class="space-y-3">
            <router-link
              v-for="puzzle in puzzles"
              :key="puzzle.id"
              :to="`/puzzles/puzzles/${puzzle.id}/pieces`"
              class="block bg-navy-900 rounded-xl p-4 border border-navy-700 hover:border-glow-500 transition-all active:scale-98"
            >
              <h3 class="text-white font-bold">{{ puzzle.name }}</h3>
              <p class="text-navy-400 text-sm mt-1">{{ puzzle.pieces_count || 0 }} {{ $t('puzzles.pieces') }}</p>
            </router-link>
          </div>
        </section>

        <div v-if="albums.length === 0 && puzzles.length === 0" class="text-center py-12">
          <div class="text-6xl mb-4">🔍</div>
          <p class="text-navy-500 text-lg">{{ $t('search.no_results') }}</p>
          <p class="text-navy-600 text-sm mt-2">{{ $t('search.try_again') }}</p>
        </div>
      </div>

      <div v-else class="text-center py-12">
        <div class="text-6xl mb-4">🔎</div>
        <p class="text-navy-500 text-lg">{{ $t('search.start_typing') }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import api from '../utils/api';
import NavBar from '../components/NavBar.vue';
import LoadingSpinner from '../components/LoadingSpinner.vue';

const { t: $t } = useI18n();

const searchQuery = ref('');
const albums = ref([]);
const puzzles = ref([]);
const searching = ref(false);
let searchTimeout = null;

async function handleSearch() {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }

  if (!searchQuery.value.trim()) {
    albums.value = [];
    puzzles.value = [];
    return;
  }

  searchTimeout = setTimeout(async () => {
    searching.value = true;

    try {
      const { data } = await api.get('/search', {
        params: { q: searchQuery.value },
      });

      albums.value = data.data.albums || [];
      puzzles.value = data.data.puzzles || [];
    } catch (error) {
      console.error('Search failed:', error);
    } finally {
      searching.value = false;
    }
  }, 300);
}
</script>

<style scoped>
.active\:scale-98:active {
  transform: scale(0.98);
}

.pb-safe {
  padding-bottom: env(safe-area-inset-bottom, 1.5rem);
}
</style>
