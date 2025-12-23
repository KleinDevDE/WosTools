<template>
  <div class="relative">
    <button
      @click="isOpen = !isOpen"
      class="flex items-center gap-2 px-3 py-2 text-glow-400 hover:text-glow-300 rounded-md hover:bg-navy-800 transition-colors"
      :title="$t('navigation.language')"
    >
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802" />
      </svg>
      <span class="text-sm font-medium uppercase hidden sm:inline">
        {{ currentLocale }}
      </span>
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 hidden sm:inline">
        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
      </svg>
    </button>

    <div
      v-if="isOpen"
      @click.self="isOpen = false"
      class="fixed inset-0 z-50"
      @keydown.esc="isOpen = false"
    >
      <div
        class="absolute right-4 mt-2 w-44 rounded-md bg-navy-800 border border-navy-600 shadow-lg"
        :class="dropdownPosition"
      >
        <button
          v-for="lang in languages"
          :key="lang.code"
          @click="switchLanguage(lang.code)"
          class="w-full text-left px-4 py-2 text-sm hover:bg-navy-700 flex items-center gap-2 transition-colors"
          :class="currentLocale === lang.code ? 'bg-navy-700 text-sky-400' : 'text-slate-300'"
        >
          <span class="text-lg">{{ lang.flag }}</span>
          <span>{{ lang.name }}</span>
          <svg
            v-if="currentLocale === lang.code"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="w-4 h-4 ml-auto text-sky-400"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';
import axios from 'axios';

const { locale } = useI18n({ useScope: 'global' });
const router = useRouter();
const isOpen = ref(false);

const languages = [
  { code: 'en', name: 'English', flag: '🇬🇧' },
  { code: 'de', name: 'Deutsch', flag: '🇩🇪' },
  { code: 'tr', name: 'Türkçe', flag: '🇹🇷' },
];

const currentLocale = computed(() => locale.value.toUpperCase());

const dropdownPosition = computed(() => {
  // Position dropdown based on viewport
  return 'top-full';
});

async function switchLanguage(newLocale) {
  try {
    // Update Vue i18n locale
    locale.value = newLocale;

    // Store in localStorage for persistence
    localStorage.setItem('locale', newLocale);

    // Update Laravel session via API
    await axios.post('/locale/switch', { locale: newLocale });

    isOpen.value = false;
  } catch (error) {
    console.error('Failed to switch language:', error);
  }
}
</script>
