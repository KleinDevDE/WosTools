<?php

namespace App\Livewire\Tables;

use App\Helpers\Permissions;
use App\Models\Role;
use App\Models\User;
use App\Services\UserInvitationService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\View;
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
                    View::make('components.player-lookup-field')
                ])
                ->action(function (array $data, Action $action) {
                    if (empty($data['player_id']) || empty($data['player_name'])) {
                        Notification::make()
                            ->title('Please enter a valid Player ID')
                            ->danger()
                            ->send();
                        $action->halt();
                    }

                    $invitation = UserInvitationService::inviteUser((int)$data['player_id'], $data['player_name']);
                    if (!$invitation) {
                        Notification::make()
                            ->title('Error creating invitation - Player may already exist')
                            ->danger()
                            ->send();
                        $action->halt();
                    }

                    Log::channel("audit")->info("Actor: ".auth()->user()->getName()." (".auth()->id().") | Invited player {$data['player_name']} (ID: {$data['player_id']})");

                    //Show modal/schema with token
                    Notification::make()
                        ->title('Invitation created')
                        ->success()
                        ->id("copy-inv-url-{$invitation->id}")
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

}
