<template>
  <div id="app" class="min-h-screen bg-navy-950">
    <router-view v-slot="{ Component }">
      <transition name="fade" mode="out-in">
        <component :is="Component" />
      </transition>
    </router-view>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useAlbumStore } from './stores/albumStore';
import { useUserStateStore } from './stores/userStateStore';
import { useMatchStore } from './stores/matchStore';
import { initializeSanctumAuth } from './utils/api';

const albumStore = useAlbumStore();
const userStateStore = useUserStateStore();
const matchStore = useMatchStore();

onMounted(async () => {
  // Initialize Sanctum authentication first
  await initializeSanctumAuth();

  // Then fetch data - albums and user states first, then matches
  await Promise.all([
    albumStore.fetchAlbums(),
    userStateStore.fetchUserStates(),
  ]);

  // Fetch matches after user states are loaded (for the navbar badge)
  await matchStore.fetchMatches();
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
