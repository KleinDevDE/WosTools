<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\WhiteoutSurvivalApiService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;

class SyncPlayerProfilesJob implements ShouldQueue
{
    use Batchable, Queueable;
    private WhiteoutSurvivalApiService $apiService;

    public function __construct(
        private readonly array $playerIDs = []
    )
    {}

    public static function prepareBatch(): array
    {
        $arrJobs = [];

        self::getQuery()->cursor()
            ->chunk(30)
            ->each(function ($users) use (&$arrJobs) {
                $playerIDs = $users->pluck('player_id')->toArray();
                $arrJobs[] = new self($playerIDs);
            });

        return $arrJobs;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->apiService = app(WhiteoutSurvivalApiService::class);
        self::getQuery()->cursor()->each(fn (User $user) => $this->syncPlayerProfile($user));
    }

    private static function getQuery(): Builder
    {
        return
            User::query()
                ->select(['player_id'])
                ->where('is_virtual', false)
                ->where('player_id', '>', 100);
    }

    private function syncPlayerProfile(User $user): void
    {
        $this->apiService->getPlayerStats($user->player_id, false);
    }
}
