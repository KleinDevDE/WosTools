<div x-data="{
    playerId: '',
    playerData: null,
    loading: false,
    error: null,

    async lookupPlayer() {
        if (!this.playerId || this.playerId.length < 5) {
            this.error = 'Please enter a valid Player ID';
            this.playerData = null;
            return;
        }

        this.loading = true;
        this.error = null;

        try {
            const response = await fetch(`/api/player/${this.playerId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                this.error = result.message || 'Player not found';
                this.playerData = null;
            } else {
                this.playerData = result.data;
                this.error = null;
            }
        } catch (err) {
            this.error = 'Failed to lookup player. Please try again.';
            this.playerData = null;
        } finally {
            this.loading = false;
        }
    }
}" class="space-y-4">
    <!-- Player ID Input -->
    <div>
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Player ID
            <span class="text-red-500">*</span>
        </label>
        <div class="mt-1 flex gap-2">
            <input
                type="number"
                x-model="playerId"
                @input.debounce.500ms="lookupPlayer()"
                placeholder="Enter Player ID"
                required
                class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
            >
        </div>

        <!-- Loading Indicator -->
        <div x-show="loading" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            <svg class="inline animate-spin h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Looking up player...
        </div>

        <!-- Error Message -->
        <div x-show="error" x-text="error" class="mt-2 text-sm text-red-600 dark:text-red-400"></div>
    </div>

    <!-- Player Preview -->
    <div x-show="playerData" class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-800">
        <div class="flex items-center gap-4">
            <!-- Avatar -->
            <img
                x-show="playerData?.playerAvatarURL"
                :src="playerData?.playerAvatarURL"
                alt="Player Avatar"
                class="w-16 h-16 rounded-full border-2 border-primary-500"
            >

            <!-- Player Info -->
            <div class="flex-1">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="playerData?.playerName"></h3>
                    <img
                        x-show="playerData?.playerAvatarURL"
                        :src="playerData?.playerAvatarURL"
                        alt="Player Avatar"
                        class="w-16 h-16 rounded-full border-2 border-primary-500"
                    >
                </div>

                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="playerData?.playerName"></h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    ID: <span x-text="playerData?.playerID"></span>
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    State: <span x-text="playerData?.state"></span>
                </p>
            </div>
        </div>
    </div>

    <!-- Hidden fields for form submission -->
    <input type="hidden" name="player_id" x-model="playerData?.playerID">
    <input type="hidden" name="player_name" x-model="playerData?.playerName">
</div>
