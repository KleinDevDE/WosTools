<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\WhiteoutSurvivalApiService;
use Illuminate\Console\Command;

use Illuminate\Support\Str;

use Spatie\Permission\Models\Role;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class ManageUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:manage-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $mode = select(
            label: 'Select an action',
            options: [
                'create' => 'Create a new user',
                'reset-password' => 'Reset a user password',
                'change-roles' => 'Change user roles',
                'update-status' => 'Update user status',
                'exit' => 'Exit'
            ]
        );

        switch ($mode) {
            case 'create':
                $this->createUser();
                break;
            case 'reset-password':
                $this->resetUserPassword();
                break;
            case 'change-roles':
                $this->changeRoles();
                break;
            case 'update-status':
                $this->updateStatus();
                break;
        }
    }

    private function createUser(): void
    {
        $isVirtual = confirm('Are you creating a virtual user?');

        $playerID = text(
            label: 'Player ID',
            validate: 'required|string'
        );

        if (User::where('player_id', $playerID)->exists()) {
            error("User with player_id $playerID already exists!");
            return;
        }

        $playerStats = null;
        if (!$isVirtual) {
            $playerStats = app(WhiteoutSurvivalApiService::class)->getPlayerStats($playerID);
        }
        $playerName = text(
            label: 'Player Name',
            default: $playerStats?->playerName ?? '',
            validate: 'required|string'
        );

        $password = text(
            label: 'Password',
            validate: 'nullable|string',
            hint: 'Leave empty to generate a random password'
        );
        if (!$password) {
            $password = Str::random();
            \Laravel\Prompts\info("Generated password: $password");
        }

        User::create([
            'player_id' => $playerID,
            'player_name' => $playerName,
            'password' => hash('sha256', $password),
            'is_virtual' => $isVirtual
        ]);
    }

    private function resetUserPassword(): void
    {
        $playerID = text(
            label: 'Player ID',
            validate: 'required|string'
        );

        if (!User::where('player_id', $playerID)->exists()) {
            error("User with player-id $playerID does not exist!");
            return;
        }

        $password = text(
            label: 'Password',
            validate: 'nullable|string',
            hint: 'Leave empty to generate a random password'
        );
        if (!$password) {
            $password = Str::random();
        }

        User::where('player_id', $playerID)->first()
            ->update(['password' => hash('sha256', $password)]);
    }

    private function updateStatus()
    {
        $playerID = text(
            label: 'Player ID',
            validate: 'required|string'
        );

        if (!User::where('player_id', $playerID)->exists()) {
            error("User with player-id $playerID does not exist!");
            return;
        }

        $user = User::where('player_id', $playerID)->first();
        $status = select(
            label: 'Status',
            options: [
                User::STATUS_ACTIVE => 'Active',
                User::STATUS_LOCKED => 'Locked',
            ],
            default: $user->status
        );

        $user->update(['status' => $status]);
    }

    private function changeRoles(): void
    {
        $playerID = text(
            label: 'Player ID',
            validate: 'required|string'
        );

        if (!User::where('player_id', $playerID)->exists()) {
            error("User with player-id $playerID does not exist!");
            return;
        }

        $user = User::where('player_id', $playerID)->first();
        $roles = multiselect(
            label: 'Role',
            options: Role::pluck('name', 'name')->toArray(),
            default: $user->roles()->pluck('name')->toArray()
        );

        $user->syncRoles($roles);
    }
}
