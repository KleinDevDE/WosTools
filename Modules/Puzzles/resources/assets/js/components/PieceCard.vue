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
      <span class="text-3xl font-bold text-navy-600">{{ piece.position }}</span>
    </div>

    <div class="absolute top-2 right-2 flex gap-1">
      <div v-if="piece.stars >= 5" class="px-2 py-0.5 bg-star-500/90 rounded-full text-xs font-bold flex items-center gap-1">
        <span>{{ piece.stars }}</span>
        <span>★</span>
      </div>
      <div v-else-if="piece.stars > 0" class="px-2 py-0.5 bg-navy-900/80 rounded-full text-xs flex items-center gap-1">
        <span>{{ piece.stars }}</span>
        <span class="text-star-500">★</span>
      </div>
    </div>

    <div v-if="userState !== 'neutral'" class="absolute inset-0 flex items-center justify-center bg-black/40">
      <div :class="iconClasses">
        <svg v-if="userState === 'need'" class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
          <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" />
        </svg>
        <svg v-else-if="userState === 'have'" class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
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
    type: String,
    default: 'neutral',
  },
});

const emit = defineEmits(['click']);

const cardClasses = computed(() => {
  const classes = ['border-2'];

  switch (props.userState) {
    case 'need':
      classes.push('border-need-500', 'shadow-lg', 'shadow-need-500/50');
      break;
    case 'have':
      classes.push('border-success-500', 'shadow-lg', 'shadow-success-500/50');
      break;
    default:
      classes.push('border-navy-700', 'hover:border-navy-600');
  }

  if (!props.piece.is_tradeable && props.userState !== 'neutral') {
    classes.push('opacity-75');
  }

  return classes;
});

const iconClasses = computed(() => {
  return props.userState === 'need'
    ? 'text-need-400'
    : 'text-success-400';
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
