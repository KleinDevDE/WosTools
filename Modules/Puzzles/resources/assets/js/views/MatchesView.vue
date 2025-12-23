<template>
  <div class="min-h-screen bg-navy-950">
    <NavBar :title="$t('matches.title')" show-back />

    <div class="max-w-7xl mx-auto px-4 py-6 pb-safe">
      <LoadingSpinner v-if="matchStore.loading" />

      <div v-else class="space-y-8">
        <section v-if="canGetFrom.length > 0">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
              <span class="text-2xl">📥</span>
              {{ $t('matches.you_can_get') }}
            </h2>
            <span class="px-3 py-1 bg-need-600/20 border border-need-500 rounded-full text-need-400 text-sm font-bold">
              {{ canGetFrom.length }} {{ $t('matches.match', canGetFrom.length) }}
            </span>
          </div>

          <div class="space-y-3">
            <div
              v-for="match in canGetFrom"
              :key="match.user.id"
              class="bg-navy-900 rounded-2xl p-4 border border-navy-700 hover:border-need-500 transition-colors"
            >
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-white font-bold">{{ match.user.name }}</h3>
                  <p class="text-navy-400 text-sm mt-1">
                    {{ match.match_count }} {{ $t('matches.piece', match.match_count) }}
                  </p>
                </div>
                <button
                  @click="copyToClipboard(match.user.name)"
                  class="px-4 py-2 bg-need-600 hover:bg-need-500 text-white font-bold rounded-xl transition-colors"
                >
                  {{ $t('matches.copy_name') }}
                </button>
              </div>
            </div>
          </div>
        </section>

        <section v-if="canHelpWith.length > 0">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
              <span class="text-2xl">🤝</span>
              {{ $t('matches.you_can_help') }}
            </h2>
            <span class="px-3 py-1 bg-success-600/20 border border-success-500 rounded-full text-success-400 text-sm font-bold">
              {{ canHelpWith.length }} {{ $t('matches.match', canHelpWith.length) }}
            </span>
          </div>

          <div class="space-y-3">
            <div
              v-for="match in canHelpWith"
              :key="match.user.id"
              class="bg-navy-900 rounded-2xl p-4 border border-navy-700 hover:border-success-500 transition-colors"
            >
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-white font-bold">{{ match.user.name }}</h3>
                  <p class="text-navy-400 text-sm mt-1">
                    {{ match.match_count }} {{ $t('matches.piece', match.match_count) }}
                  </p>
                </div>
                <button
                  @click="copyToClipboard(match.user.name)"
                  class="px-4 py-2 bg-success-600 hover:bg-success-500 text-white font-bold rounded-xl transition-colors"
                >
                  {{ $t('matches.copy_name') }}
                </button>
              </div>
            </div>
          </div>
        </section>

        <div v-if="canGetFrom.length === 0 && canHelpWith.length === 0 && !matchStore.loading" class="text-center py-12">
          <div class="text-6xl mb-4">🤷</div>
          <p class="text-navy-500 text-lg">{{ $t('matches.no_matches') }}</p>
        </div>

        <button
          v-if="!matchStore.loading"
          @click="refreshMatches"
          class="w-full py-3 bg-glow-600 hover:bg-glow-500 text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          {{ $t('matches.refresh') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useMatchStore } from '../stores/matchStore';
import NavBar from '../components/NavBar.vue';
import LoadingSpinner from '../components/LoadingSpinner.vue';

const matchStore = useMatchStore();

const canGetFrom = computed(() => matchStore.canGetFrom);
const canHelpWith = computed(() => matchStore.canHelpWith);

async function refreshMatches() {
  await matchStore.fetchMatches(true);
}

async function copyToClipboard(text) {
  try {
    await navigator.clipboard.writeText(text);
  } catch (error) {
    console.error('Failed to copy:', error);
  }
}

onMounted(async () => {
  await matchStore.fetchMatches();
});
</script>

<style scoped>
.pb-safe {
  padding-bottom: env(safe-area-inset-bottom, 1.5rem);
}
</style>
