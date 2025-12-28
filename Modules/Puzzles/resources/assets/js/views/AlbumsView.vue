<template>
  <div class="min-h-screen bg-navy-950">
    <NavBar :title="$t('albums.title', 2)" />
      <div class="p-6">
          <router-link
              v-if="matchCount > 0"
              to="/puzzles/matches"
              class="flex items-center justify-self-center p-2 text-glow-400 transition-colors relative"
          >
              <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              {{ $t('albums.found_matches', { count: matchCount }) }}
          </router-link>
      </div>

    <div class="max-w-7xl mx-auto px-4 py-6 pb-safe">
      <LoadingSpinner v-if="albumStore.loading && albums.length === 0" />

      <div v-else class="space-y-4">
        <router-link
          v-for="album in albums"
          :key="album.id"
          :to="`/puzzles/albums/${album.id}/puzzles`"
          class="block bg-navy-900 rounded-2xl overflow-hidden border border-navy-700 hover:border-glow-500 transition-all active:scale-98"
        >
          <div class="flex items-center p-4">
            <div v-if="album.cover_url" class="shrink-0 w-20 h-20 rounded-xl overflow-hidden">
              <img :src="album.cover_url" :alt="album.name" class="w-full h-full object-cover" loading="lazy" />
            </div>
            <div v-else class="shrink-0 w-20 h-20 rounded-xl bg-navy-800 flex items-center justify-center">
              <svg class="w-10 h-10 text-navy-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm2 0v8h12V6H4z" />
              </svg>
            </div>

            <div class="flex-1 ml-4 min-w-0">
              <h3 class="text-lg font-bold text-white truncate">{{ album.name }}</h3>
              <p class="text-sm text-navy-400 mt-1">{{ album.puzzles_count || 0 }} {{ $t('albums.puzzles') }}</p>

              <div v-if="album.total_pieces > 0" class="mt-3">
                <div class="flex items-center justify-between text-xs mb-1">
                  <span class="text-navy-400">{{ $t('albums.progress') }}</span>
                  <span class="text-glow-400 font-bold">{{ album.completion_percentage }}%</span>
                </div>
                <div class="h-2 bg-navy-800 rounded-full overflow-hidden">
                  <div
                    class="h-full bg-linear-to-r from-glow-600 to-glow-400 rounded-full transition-all duration-500"
                    :style="{ width: `${album.completion_percentage}%` }"
                  />
                </div>
              </div>
            </div>

            <svg class="shrink-0 w-6 h-6 text-navy-600 ml-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </div>
        </router-link>

        <div v-if="albums.length === 0 && !albumStore.loading" class="text-center py-12">
          <p class="text-navy-500 text-lg">{{ $t('albums.no_albums') }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { useAlbumStore } from '../stores/albumStore';
import { useMatchStore } from '../stores/matchStore';
import NavBar from '../components/NavBar.vue';
import LoadingSpinner from '../components/LoadingSpinner.vue';

const { t: $t } = useI18n();

const albumStore = useAlbumStore();
const matchStore = useMatchStore();

const albums = computed(() => albumStore.albums);
const matchCount = computed(() => matchStore.totalMatches);

onMounted(async () => {
  await albumStore.fetchAlbums();
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
