<template>
  <div
    class="piece-card relative aspect-square rounded-2xl overflow-hidden cursor-pointer transition-all duration-200"
    :class="cardClasses"
    @click="handleClick"
  >
    <div v-if="piece.image_url" class="w-full h-full">
      <img
        :src="piece.image_url"
        :alt="`Piece ${piece.position}`"
        class="w-full h-full object-cover"
        loading="lazy"
      />
    </div>

    <div v-else class="w-full h-full bg-navy-800 flex items-center justify-center">
      <span class="text-3xl font-bold text-navy-300">{{ piece.position }}</span>
    </div>

    <div class="absolute bottom-2 mx-auto flex gap-1 justify-self-center">
      <div v-if="piece.stars >= 5" class="px-2 py-0.5 bg-star-500/90 rounded-full text-xs font-bold flex items-center gap-1">
        <span>{{ piece.stars }}</span>
        <span>★</span>
      </div>
      <div v-else-if="piece.stars > 0" class="px-2 py-0.5 bg-navy-900/80 rounded-full text-xs flex items-center gap-1">
          <span v-for="n in piece.stars" class="text-star-500">★</span>
      </div>
    </div>

    <div v-if="hasAnyState" class="absolute top-2 left-2 flex gap-1">
      <div v-if="userState.needs" class="px-2 py-1 bg-need-500/90 rounded-lg text-white text-xs font-bold flex items-center gap-1">
        <span>📥</span>
        <span>Need</span>
      </div>
      <div v-if="userState.owns" class="px-2 py-1 bg-navy-500/90 rounded-lg text-white text-xs font-bold flex items-center gap-1">
        <span>📦</span>
        <span>Own</span>
      </div>
      <div v-if="userState.offers > 0" class="px-2 py-1 bg-success-500/90 rounded-lg text-white text-xs font-bold flex items-center gap-1">
        <span>✅</span>
        <span>× {{ userState.offers }}</span>
      </div>
    </div>

    <div v-if="!piece.is_tradeable" class="absolute bottom-0 inset-x-0 bg-red-600/90 text-white text-xs text-center py-1 font-bold">
      Cannot Trade
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  piece: {
    type: Object,
    required: true,
  },
  userState: {
    type: Object,
    default: () => ({ needs: false, owns: false, offers: 0 }),
  },
});

const emit = defineEmits(['click']);

const hasAnyState = computed(() => {
  return props.userState.needs || props.userState.owns || props.userState.offers > 0;
});

const cardClasses = computed(() => {
  const classes = ['border-2'];

  // Priority: needs > offers > owns > neutral
  if (props.userState.needs) {
    classes.push('border-need-500', 'shadow-lg', 'shadow-need-500/50');
  } else if (props.userState.offers > 0) {
    classes.push('border-success-500', 'shadow-lg', 'shadow-success-500/50');
  } else if (props.userState.owns) {
    classes.push('border-navy-500');
  } else {
    classes.push('border-navy-700', 'hover:border-navy-600', 'opacity-60', 'hover:opacity-100');
  }

  return classes;
});

function handleClick() {
  emit('click', props.piece);
}
</script>

<style scoped>
.piece-card {
  -webkit-tap-highlight-color: transparent;
  touch-action: manipulation;
}

.piece-card:active {
  transform: scale(0.95);
}
</style>
