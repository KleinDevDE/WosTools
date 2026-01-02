<?php

namespace App\Console\Commands;

use App\Objects\PlayerInfo;
use App\Services\WhiteoutSurvivalApiService;
use Illuminate\Console\Command;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\table;

class FetchPlayerData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wos:fetch-player {player_id : The Whiteout Survival player ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch player data from Whiteout Survival API by player ID';

    /**
     * Execute the console command.
     */
    public function handle(WhiteoutSurvivalApiService $apiService): int
    {
        $playerId = $this->argument('player_id');

        info("Fetching player data for ID: {$playerId}");

        $playerData = $apiService->getPlayerStats($playerId);

        if ($playerData === null) {
            error("Failed to fetch player data for ID {$playerId}. This may be due to an invalid player ID, network connectivity issues, or a problem with the Whiteout Survival API. Please verify the ID and check the application logs for more details.");
            return self::FAILURE;
        }

        $this->displayPlayerData($playerData);

        return self::SUCCESS;
    }

    /**
     * Display player data in a formatted table
     *
     * @param PlayerInfo $playerStats
     * @return void
     */
    private function displayPlayerData(PlayerInfo $playerStats): void
    {
        $this->newLine();
        $this->line('<fg=green>Player Information:</>');
        $this->newLine();

        // Prepare data for table display
        $tableData = [];
        foreach ($playerStats->toArray() as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_PRETTY_PRINT);
            }
            $tableData[] = [
                'Field' => $key,
                'Value' => $value,
            ];
        }

        table(
            headers: ['Field', 'Value'],
            rows: $tableData
        );

        $this->newLine();
    }
}
