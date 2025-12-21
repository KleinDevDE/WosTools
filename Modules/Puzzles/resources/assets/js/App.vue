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
import { initializeSanctumAuth } from './utils/api';

const albumStore = useAlbumStore();
const userStateStore = useUserStateStore();

onMounted(async () => {
  // Initialize Sanctum authentication first
  await initializeSanctumAuth();

  // Then fetch data
  await Promise.all([
    albumStore.fetchAlbums(),
    userStateStore.fetchUserStates(),
  ]);
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
