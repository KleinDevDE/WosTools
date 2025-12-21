<template>
  <teleport to="body">
    <transition name="overlay">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-end"
        @click="handleBackdropClick"
      >
        <div class="absolute inset-0 bg-black/60" />

        <div
          ref="sheetRef"
          class="relative w-full bg-navy-900 rounded-t-3xl max-h-[90vh] flex flex-col"
          :class="{ 'slide-up': isOpen, 'slide-down': !isOpen }"
          @touchstart="handleTouchStart"
          @touchmove="handleTouchMove"
          @touchend="handleTouchEnd"
        >
          <div class="flex-shrink-0 py-3 px-4 border-b border-navy-700">
            <div class="w-12 h-1.5 bg-navy-600 rounded-full mx-auto mb-3" />
            <h2 v-if="title" class="text-xl font-bold text-white">
              {{ title }}
            </h2>
          </div>

          <div class="flex-1 overflow-y-auto p-4">
            <slot />
          </div>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  modelValue: Boolean,
  title: String,
});

const emit = defineEmits(['update:modelValue', 'close']);

const sheetRef = ref(null);
const isOpen = ref(false);
const touchStartY = ref(0);
const touchCurrentY = ref(0);

watch(() => props.modelValue, (newVal) => {
  isOpen.value = newVal;
  if (newVal) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
});

function close() {
  isOpen.value = false;
  setTimeout(() => {
    emit('update:modelValue', false);
    emit('close');
  }, 300);
}

function handleBackdropClick(e) {
  if (e.target === e.currentTarget) {
    close();
  }
}

function handleTouchStart(e) {
  touchStartY.value = e.touches[0].clientY;
}

function handleTouchMove(e) {
  touchCurrentY.value = e.touches[0].clientY;
  const diff = touchCurrentY.value - touchStartY.value;

  if (diff > 0 && sheetRef.value) {
    sheetRef.value.style.transform = `translateY(${diff}px)`;
  }
}

function handleTouchEnd() {
  const diff = touchCurrentY.value - touchStartY.value;

  if (sheetRef.value) {
    sheetRef.value.style.transform = '';
  }

  if (diff > 100) {
    close();
  }

  touchStartY.value = 0;
  touchCurrentY.value = 0;
}
</script>

<style scoped>
.overlay-enter-active,
.overlay-leave-active {
  transition: opacity 0.3s ease;
}

.overlay-enter-from,
.overlay-leave-to {
  opacity: 0;
}
</style>
