<?php

namespace App\Livewire\Tables;

use App\Helpers\Permissions;
use App\Models\CharacterStats;
use App\Models\User;
use Silber\Bouncer\Database\Role;
use App\Objects\CharacterStatsObject;
use App\Services\UserInvitationService;
use App\Services\WhiteoutSurvivalApiService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class UsersTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->selectable();
    }

    protected function getTableFilters(): array
    {
        return [];
    }

    public function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->sortable(),
            TextColumn::make('player_id')
                ->searchable()
                ->iconPosition(IconPosition::After)->icon(Heroicon::Clipboard)
                ->copyable()->copyMessageDuration(500)
                ->sortable(),
            TextColumn::make('player_name')
                ->searchable()
                ->sortable(),
            TextColumn::make('display_name')
                ->searchable()
                ->sortable()
                ->placeholder('—'),
            TextColumn::make('locale')
                ->formatStateUsing(fn(string $state) => ['en' => 'English', 'de' => 'German', 'tr' => 'Turkish'][$state] ?? "N/A"),
            TextColumn::make('last_login_at', 'last_login_at')
                ->sortable(),
            TextColumn::make('characters_count')
                ->label('Characters')
                ->counts('characters')
                ->sortable(),
        ];
    }

    public function getTableBulkActions(): array
    {
        return [];
    }

    protected function getTableHeaderActions(): array
    {
        $actions = [];

        if (auth()->user()->can(Permissions::USERS_INVITE)) {
            $actions[] = Action::make('inviteUser')
                ->label('Invite User')
                ->icon('heroicon-o-plus')
                ->modalWidth(Width::Small)
                ->schema([
                    Hidden::make('manual_mode')
                        ->default(false),

                    TextInput::make('player_id')
                        ->label('Player ID')
                        ->numeric()
                        ->required()
                        ->live(debounce: 500)
                        ->hintAction(
                            Action::make('toggleManualMode')
                                ->label(fn ($get) => $get('manual_mode')
                                    ? 'Zurück zum API-Modus'
                                    : 'Name manuell eintragen'
                                )
                                ->icon(fn ($get) => $get('manual_mode')
                                    ? 'heroicon-o-arrow-path'
                                    : 'heroicon-o-pencil'
                                )
                                ->color(fn ($get) => $get('manual_mode') ? 'success' : 'gray')
                                ->action(function ($set, $get) {
                                    $isManual = !$get('manual_mode');
                                    $set('manual_mode', $isManual);

                                    if ($isManual) {
                                        $set('player_name', null);
                                        $set('player_preview', null);
                                        if (empty($get('player_id'))) {
                                            $set('player_id', '0');
                                        }
                                    }
                                })
                        )
                        ->afterStateUpdated(function ($state, $set, $get) {
                            // Skip API call if in manual mode
                            if ($get('manual_mode')) {
                                return;
                            }

                            if (empty($state) || strlen($state) < 5) {
                                $set('player_name', null);
                                $set('player_preview', null);
                                return;
                            }

                            // Trigger Alpine.js to fetch player data
                            // The actual fetch will be done by Alpine.js

                            $playerStats = app(WhiteoutSurvivalApiService::class)->getPlayerStats((int)$state);
                            if (!$playerStats) {
                                $set('player_name', null);
                                $set('player_preview', null);
                                return;
                            }

                            $set('player_name', $playerStats->playerName);
                            $set('player_preview', json_encode($playerStats));
                        }),

                    TextInput::make('player_name')
                        ->label('Player Name')
                        ->required()
                        ->disabled(fn ($get) => !$get('manual_mode'))
                        ->dehydrated(true)
                        ->placeholder(fn ($get) => $get('manual_mode')
                            ? 'Spielernamen eingeben'
                            : 'Wird automatisch geladen...'
                        )
                        ->helperText(fn ($get) => $get('manual_mode')
                            ? 'Manueller Modus - Namen direkt eingeben'
                            : 'Wird automatisch über API geladen'
                        ),

                    Placeholder::make('manual_mode_indicator')
                        ->label('')
                        ->content(new HtmlString(
                            '<div class="rounded-lg border border-blue-200 dark:border-blue-700 p-3 bg-blue-50 dark:bg-blue-900/20">' .
                            '<p class="text-sm text-blue-700 dark:text-blue-300">' .
                            '<svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">' .
                            '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>' .
                            '</svg>' .
                            'Manueller Modus aktiv - API-Aufruf übersprungen' .
                            '</p>' .
                            '</div>'
                        ))
                        ->visible(fn ($get) => $get('manual_mode')),

                    Hidden::make('player_preview'),

                    Placeholder::make('player_display')
                        ->label('Preview')
                        ->content(fn ($get) => $this->renderPlayerPreview($get('player_preview')))
                        ->visible(fn ($get) => !$get('manual_mode') && !empty($get('player_preview'))),
                ])
                ->action(function (array $data, Action $action) {
                    if (empty($data['player_id']) || empty($data['player_name'])) {
                        Notification::make()
                            ->title('player_id or player_name missing')
                            ->danger()
                            ->send();
                        $action->halt();
                    }

                    $user = User::query()->where('player_id', $data['player_id'])->first();
                    if ($user) {
                        Notification::make()
                            ->title('Player already exists')
                            ->danger()
                            ->send();
                        $action->halt();
                    }

                    $invitation = UserInvitationService::inviteUser(
                        (int)$data['player_id'],
                        $data['player_name'],
                        $data['manual_mode'] ?? false
                    );
                    if (!$invitation) {
                        Notification::make()
                            ->title('Error creating invitation!')
                            ->danger()
                            ->send();
                        $action->halt();
                    }

                    Log::channel("audit")->info(
                        "Actor: ".auth()->user()->getName()." (".auth()->id().") | " .
                        "Invited player {$data['player_name']} (ID: {$data['player_id']})" .
                        ($data['manual_mode'] ?? false ? " [MANUAL MODE]" : "")
                    );

                    //Show modal/schema with token
                    Notification::make()
                        ->title('Invitation created')
                        ->success()
                        ->body($invitation->invitationURL)
                        ->duration(10000)
                        ->actions([
                            Action::make("copy-inv-url-$invitation->id")
                                ->label('Copy')
                                ->button()
                                ->extraAttributes(['id' => "copy-inv-url-$invitation->id"])
                                ->dispatch(
                                    'copy-to-clipboard',
                                    ['text' => $invitation->invitationURL, 'element' => "copy-inv-url-$invitation->id"]
                                )
                        ])
                        ->send();
                });
        }

        return $actions;
    }

    protected function getTableActions(): array
    {
        return [
            EditAction::make('edit')
                ->schema([
                    Select::make('locale')
                        ->label('Language')
                        ->options(['en' => 'English', 'de' => 'German', 'tr' => 'Turkish'])
                        ->required(),
                ])
                ->action(function (User $user, array $data): void {
                    $user->update([
                        'locale' => $data['locale'],
                    ]);

                    Log::channel("audit")->info(
                        "Actor: " . (auth('character')->user()?->getName() ?? 'System') . " | " .
                        "Changed locale of user " . $user->player_name . " to " . $data['locale']
                    );

                    Notification::make()
                        ->title('User updated successfully')
                        ->success()
                        ->send();
                }),
            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Delete User')
                ->modalDescription(fn(User $user) =>
                    "Are you sure you want to delete user '{$user->player_name}'? This will delete all associated characters and data. This action cannot be undone."
                )
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('User deleted')
                        ->body('The user and all associated characters have been deleted successfully.')
                ),
        ];
    }

    protected function renderPlayerPreview(?string $playerDataJson): HtmlString
    {
        if (empty($playerDataJson)) {
            return new HtmlString('');
        }

        $playerData = json_decode($playerDataJson, true);
        if (!$playerData) {
            return new HtmlString('');
        }

        $html = '<div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-800">';
        $html .= '<div class="flex items-center gap-4">';

        // Avatar
        if (!empty($playerData['playerAvatarURL'])) {
            $html .= '<img src="' . e($playerData['playerAvatarURL']) . '" alt="Player Avatar" class="w-16 h-16 rounded-full border-2 border-primary-500">';
        }

        // Player Info
        $html .= '<div class="flex-1">';
        $html .= '<div class="flex justify-start items-center">';

        if (!empty($playerData['furnaceLevelIcon'])) {
            if($playerData['furnaceLevel'] > 30) {
                $html .= '<img src="' . e($playerData['furnaceLevelIcon']) . '" alt="Furnace Level" class="w-8 h-8">';
            } else {
                $html .= '<div class="w-8 h-8 text-center align-middle leading-8 font-bold text-white rounded-full mr-2 flex items-center justify-center p-0 bg-glow-500">' . e($playerData['furnaceLevel'] ?? '') . '</div>';
            }
        }

        $html .= '<h3 class="text-lg font-semibold text-gray-900 dark:text-white">' . e($playerData['playerName'] ?? '') . '</h3>';
        $html .= '</div>';
        $html .= '<p class="text-sm text-gray-500 dark:text-gray-400">ID: ' . e($playerData['playerID'] ?? '') . '</p>';
        $html .= '<p class="text-sm text-gray-500 dark:text-gray-400">State: ' . e($playerData['state'] ?? '') . '</p>';
        $html .= '</div>';

        $html .= '</div>';
        $html .= '</div>';

        return new HtmlString($html);
    }

}
