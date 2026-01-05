<?php

namespace App\Livewire\Tables;

use App\Helpers\Permissions;
use App\Models\PlayerProfile;
use App\Models\Role;
use App\Models\User;
use App\Objects\PlayerInfo;
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
        return [
            SelectFilter::make('status')
                ->options([
                    User::STATUS_ACTIVE => 'Active',
                    User::STATUS_LOCKED => 'Locked',
                    User::STATUS_INVITED => 'Invited',
                ]),
        ];
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
            TextColumn::make('roles')
                ->getStateUsing(fn(User $record) => $record->roles->sortBy('weight')->pluck('name')->join(', '))
                ->sortable(),
            TextColumn::make('status')
                ->badge()
                ->color(fn(User $record) => match ($record->status) {
                    User::STATUS_ACTIVE => 'success',
                    User::STATUS_LOCKED => 'danger',
                    User::STATUS_INVITED => 'primary',
                    default => 'gray',
                })
                ->sortable(),
        ];
    }

    public function getTableBulkActions(): array
    {
        $actions = [];
        if (auth()->user()->can(Permissions::USERS_LOCK)) {
            $actions[] = BulkAction::make('Lock')
                //Confirm
                ->requiresConfirmation()
                ->action(function (Collection $records): void {
                    foreach ($records as $record) {
                        $record->update(['status' => User::STATUS_LOCKED]);
                    }
                });
        }

        if (auth()->user()->can(Permissions::USERS_LOCK)) {
            $actions[] = BulkAction::make('Unlock')
                ->requiresConfirmation()
                ->action(function (Collection $records): void {
                    foreach ($records as $record) {
                        $record->update(['status' => User::STATUS_ACTIVE]);
                    }
                });
        }

        $actions[] = BulkAction::make('edit')
            ->requiresConfirmation()
            ->schema([
                Select::make('status')
                ->options([
                    User::STATUS_ACTIVE => 'Active',
                    User::STATUS_LOCKED => 'Locked',
                    User::STATUS_INVITED => 'Invited',
                ])
            ])
        ->action(fn (Collection $records, array $data) => $this->updateStatus($data['status']));
        return $actions;
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
        $selectableRoles = Role::query()->get()
            ->mapWithKeys(function(Role $role) {
                $displayName = $role->name;
                if (!auth()->user()->canManageRole($role)) {
                    $displayName .= " - (No rights to change role, read-only)";
                    return [$role->name => $displayName];
                }

                return [$role->name => $displayName];
            })->toArray();

        return [
            Action::make('copy_inv_url')
                //Only if status === invited
                ->hidden(fn(User $user) => $user->status !== User::STATUS_INVITED)
                ->label("Copy Invitation URL")
                ->button()
                ->icon(Heroicon::Clipboard)
                ->iconPosition(IconPosition::After)
                ->extraAttributes(fn(User $user) => ['id' => "copy-inv-url-$user->id"])
                ->dispatch('copy-to-clipboard', fn(User $user) => [
                    'text' => $user->invitations()->first()?->invitationURL,
                    'element' => "copy-inv-url-$user->id"
                ]),
            Action::make('lock')
                ->requiresConfirmation()
                ->visible(fn(User $user) =>
                    auth()->user()->can(Permissions::USERS_LOCK)
                    && $user->status === User::STATUS_ACTIVE
                    && $user->id !== auth()->id()
                    && auth()->user()->canManageUser($user)
                )
                ->icon(Heroicon::LockClosed)
                ->button()->color('danger')
                ->size(Size::ExtraSmall)
                ->action(function(User $user) {
                    $this->updateStatus(User::STATUS_LOCKED, $user);
                    \Log::channel("audit")->info("Actor: ".auth()->user()->getName()." (".auth()->id().") | Locked user ".$user->getName());
                    Notification::make()
                        ->title('User locked')
                        ->success()
                        ->duration(10000)
                        ->send();
                }),
            Action::make('unlock')
                ->requiresConfirmation()
                ->button()->color('success')
                ->size(Size::ExtraSmall)
                ->visible(fn(User $user) =>
                    auth()->user()->can(Permissions::USERS_LOCK)
                    && $user->status === User::STATUS_LOCKED
                    && $user->id !== auth()->id()
                    && auth()->user()->canManageUser($user)
                )
                ->icon(Heroicon::LockOpen)
                ->action(function(User $user) {
                    $this->updateStatus(User::STATUS_ACTIVE, $user);
                    \Log::channel("audit")->info("Actor: ".auth()->user()->getName()." (".auth()->id().") | Unlocked user ".$user->getName());
                    Notification::make()
                        ->title('User unlocked')
                        ->success()
                        ->duration(10000)
                        ->send();
                }),
            EditAction::make('edit')
                ->visible(fn(User $user) => auth()->user()->canManageUser($user))
                ->schema([
                    TextInput::make('player_name')
                        ->label('Player Name')
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('display_name')
                        ->label('Display Name')
                        ->placeholder('Optional - overrides player name'),
                    Select::make('locale')
                        ->options(['en' => 'English', 'de' => 'German', 'tr' => 'Turkish'])
                        ->afterStateUpdated(function (Select $component, User $user) {
                            $select = $component->getContainer()->getComponent('roles');
                            $select->state($user->locale);
                        }),
                    Select::make('roles')
                        ->multiple()
                        ->options($selectableRoles)
                        ->afterStateHydrated(function (Select $component, User $user) {
                            $select = $component->getContainer()->getComponent('roles');
                            $select->state($user->roles->pluck('name')->toArray());
                        })
                ])
                ->before(function (EditAction $action, User $user, array $data): void {
                    $userRoles = $user->roles->pluck('name')->toArray();

                    //Allow if the role is lower than auth user
                    $data['roles'] = array_filter($data['roles'], function ($roleName) {
                        return auth()->user()->canManageRole($roleName);
                    });

                    //Add currently linked roles if they are higher than auth user
                    foreach ($userRoles as $roleName) {
                        if (!auth()->user()->canManageRole($roleName)) {
                            $data['roles'][] = $roleName;
                        }
                    }

                    //Skip if no changes
                    if (empty(array_diff($data['roles'], $userRoles))) {
                        return;
                    }

                    Log::channel("audit")->info("Actor: ".auth()->user()->getName()." (".auth()->id().") | Changed roles of user ".$user->getName()." to ".implode(", ", $data['roles']));

                    $user->syncRoles($data['roles']);
                })
            ,
            DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }

    public function updateStatus(string $status, ?User $user = null): void
    {
        if ($user === null && empty($this->getSelectedTableRecordsQuery())) {
            return;
        }

        if (!empty($user)) {
            Log::channel("audit")->info("Actor: ".auth()->user()->getName()." (".auth()->id().") | Changed status of user ".$user->getName()." to $status");
        } else {
            foreach ($this->getSelectedTableRecordsQuery()->get() as $record) {
                Log::channel("audit")->info("Actor: ".auth()->user()->getName()." (".auth()->id().") | Changed status of user ".$record->getName()." to $status");
            }
        }

        ($user ?? $this->getSelectedTableRecordsQuery())->update(['status' => $status]);
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
