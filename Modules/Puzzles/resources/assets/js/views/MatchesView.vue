<template>
    <div class="min-h-screen bg-navy-950">
        <NavBar :title="$t('matches.title')" show-back/>

        <div class="max-w-7xl mx-auto px-4 py-6 pb-safe">
            <LoadingSpinner v-if="matchStore.loading"/>

            <div v-else class="space-y-8">
                <section v-if="canGetFrom.length > 0">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <span class="text-2xl">📥</span>
                            {{ $t('matches.you_can_get') }}
                        </h2>
                        <span
                            class="px-3 py-1 bg-need-600/20 border border-need-500 rounded-full text-need-400 text-sm font-bold">
              {{ canGetFrom.length }} {{ $t('matches.match', canGetFrom.length) }}
            </span>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="match in canGetFrom"
                            :key="`get-${match.piece_id}-${match.user.id}`"
                            class="bg-navy-900 rounded-2xl p-4 border border-navy-700 hover:border-need-500 transition-colors relative"
                        >
                            <div class="absolute top-0 right-2 gap-1 mt-2">
                                <span v-for="n in match.stars" class="text-star-500">★</span>
                            </div>

                            <div class="space-y-3 flex">
                                <div class="flex items-center">
                                    <div
                                        class="px-6 py-4 text-center text-2xl font-bold text-need-400 inline-flex flex-col overflow-hidden rounded-2xl border-2 border-need-500 bg-need-600/20">
                                        #{{ match.position }}
                                    </div>
                                </div>

                                <div class="text-center pl-4">
                                    <div class="text-xl font-bold text-white mb-1">
                                        {{ match.album_name }}
                                    </div>
                                    <div class="text-lg font-semibold text-navy-300">
                                        {{ match.puzzle_name }}
                                    </div>
                                </div>
                            </div>


                            <div class="grid grid-cols-2 border-t border-navy-700">
                                <!-- User info and action -->
                                <div class="flex items-center justify-between pt-2 text-xs">
                                    <div @click="copyToClipboard(match.user.username)">
                                        <div class="text-navy-400 mb-1">
                                            {{ $t('matches.from') }}
                                        </div>
                                        <div class="flex text-white font-bold">
                                            {{ match.user.username }}
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                 fill="currentColor" class="size-4 text-navy-500">
                                                <path fill-rule="evenodd"
                                                      d="M10.5 3A1.501 1.501 0 0 0 9 4.5h6A1.5 1.5 0 0 0 13.5 3h-3Zm-2.693.178A3 3 0 0 1 10.5 1.5h3a3 3 0 0 1 2.694 1.678c.497.042.992.092 1.486.15 1.497.173 2.57 1.46 2.57 2.929V19.5a3 3 0 0 1-3 3H6.75a3 3 0 0 1-3-3V6.257c0-1.47 1.073-2.756 2.57-2.93.493-.057.989-.107 1.487-.15Z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-end text-right pt-2 text-xs">
                                    <div>
                                        <div class="mb-1">
                                            {{ $t('matches.last_updated') }} UTC
                                        </div>
                                        <div>
                                            {{ match.last_updated }}
                                        </div>
                                    </div>
                                </div>
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
                        <span
                            class="px-3 py-1 bg-success-600/20 border border-success-500 rounded-full text-success-400 text-sm font-bold">
              {{ canHelpWith.length }} {{ $t('matches.match', canHelpWith.length) }}
            </span>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="match in canHelpWith"
                            :key="`help-${match.piece_id}-${match.user.id}`"
                            class="bg-navy-900 rounded-2xl p-4 border border-navy-700 hover:border-success-500 transition-colors"
                        >
                            <div class="space-y-3">
                                <!-- Piece number as big badge -->
                                <div class="flex items-center justify-center">
                                    <div
                                        class="bg-success-600/20 border-2 border-success-500 rounded-2xl px-6 py-4 inline-block">
                                        <div class="text-4xl font-bold text-success-400">
                                            #{{ match.position }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Album and Puzzle name -->
                                <div class="text-center">
                                    <div class="text-xl font-bold text-white mb-1">
                                        {{ match.album_name }}
                                    </div>
                                    <div class="text-lg font-semibold text-navy-300">
                                        {{ match.puzzle_name }}
                                    </div>
                                    <div class="flex items-center justify-center gap-1 mt-2">
                                        <span class="text-navy-400">{{ match.stars }}</span>
                                        <span class="text-star-500">★</span>
                                    </div>
                                </div>

                                <!-- User info and action -->
                                <div class="flex items-center justify-between pt-2 border-t border-navy-700">
                                    <div class="text-left">
                                        <div class="text-xs text-navy-400 mb-1">
                                            {{ $t('matches.needs_from') }}
                                        </div>
                                        <div class="text-white font-bold">
                                            {{ match.user.username }}
                                        </div>
                                    </div>
                                    <button
                                        @click="copyToClipboard(match.user.username)"
                                        class="px-4 py-2 bg-success-600 hover:bg-success-500 text-white text-sm font-bold rounded-lg transition-colors"
                                    >
                                        {{ $t('matches.copy_name') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div v-if="!lastFetch" class="text-center py-12">
                    <p class="text-navy-500 text-lg">{{ $t('matches.refresh') }}</p>
                </div>

                <div v-if="canGetFrom.length === 0 && canHelpWith.length === 0 && !matchStore.loading && lastFetch"
                     class="text-center py-12">
                    <div class="text-6xl mb-4">🤷</div>
                    <p class="text-navy-500 text-lg">{{ $t('matches.no_matches') }}</p>
                </div>

                <button
                    v-if="!matchStore.loading"
                    @click="refreshMatches"
                    class="w-full py-3 bg-glow-600 hover:bg-glow-500 text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ $t('matches.refresh') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import {computed, onMounted} from 'vue';
import {useMatchStore} from '../stores/matchStore';
import NavBar from '../components/NavBar.vue';
import LoadingSpinner from '../components/LoadingSpinner.vue';

const matchStore = useMatchStore();

const canGetFrom = computed(() => matchStore.canGetFrom);
const canHelpWith = computed(() => matchStore.canHelpWith);
const lastFetch = computed(() => matchStore.lastFetch);

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
    await refreshMatches();
});
</script>

<style scoped>
.pb-safe {
    padding-bottom: env(safe-area-inset-bottom, 1.5rem);
}
</style>
