<script setup lang="ts">
const props = defineProps({
    match: {
        type: Object,
        required: true,
    }
});

async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
    } catch (error) {
        console.error('Failed to copy:', error);
    }
}
</script>
<template>
    <div
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
                        {{ $t('matches.player') }}
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
                        {{ $t('matches.last_updated') }}
                    </div>
                    <div>
                        {{ match.last_updated }} UTC
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
