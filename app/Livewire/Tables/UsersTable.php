<?php

namespace App\Livewire\Tables;

use App\Helpers\Permissions;
use App\Models\User;
use App\Services\UserInvitationService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Collection;
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
            TextColumn::make('username')
                ->searchable()
                ->iconPosition(IconPosition::After)->icon(Heroicon::Clipboard)
                ->copyable()->copyMessageDuration(500)
                ->sortable(),
            TextColumn::make('last_login_at', 'last_login_at')
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
            BulkAction::make('Lock')
                //Confirm
                ->requiresConfirmation()
                ->action(function (Collection $records): void {
                    foreach ($records as $record) {
                        $record->update(['status' => User::STATUS_LOCKED]);
                    }
                });
        }

        if (auth()->user()->can(Permissions::USERS_LOCK)) {
            BulkAction::make('Unlock')
                ->requiresConfirmation()
                ->action(function (Collection $records): void {
                    foreach ($records as $record) {
                        $record->update(['status' => User::STATUS_ACTIVE]);
                    }
                });
        }

        return $actions;
    }

    protected function getTableHeaderActions(): array
    {
        $actions = [];

        if (auth()->user()->can(Permissions::USERS_INVITE)) {
            $actions[] = Action::make('inviteUser')
                ->label('Invite User')
                ->icon('heroicon-o-plus')
                ->schema([
                    TextInput::make('username')
                        ->required()
                        ->rule('unique:users,username'),
                ])
                ->action(function (array $data, Action $action) {
                    $invitation = UserInvitationService::inviteUser($data['username']);
                    if (!$invitation) {
                        Notification::make()
                            ->title('Error creating invitation')
                            ->danger()
                            ->send();
                        $action->halt();
                    }

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
                ->visible(fn(User $user) => $user->status === User::STATUS_ACTIVE && $user->id !== auth()->id())
                ->icon(Heroicon::LockClosed)
                ->button()->color('danger')
                ->size(Size::ExtraSmall)
                ->action(function(User $user) {
                    $this->updateStatus(User::STATUS_LOCKED, $user);
                    \Log::channel("audit")->info("User $user->username locked by ".auth()->user()->username);
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
                ->visible(fn(User $user) => $user->status === User::STATUS_LOCKED && $user->id !== auth()->id())
                ->icon(Heroicon::LockOpen)
                ->action(function(User $user) {
                    $this->updateStatus(User::STATUS_ACTIVE, $user);
                    \Log::channel("audit")->info("User $user->username unlocked by ".auth()->user()->username);
                    Notification::make()
                        ->title('User unlocked')
                        ->success()
                        ->duration(10000)
                        ->send();
                }),
            EditAction::make('edit')
                ->schema([
                    TextInput::make('username')
                        ->required()->unique(User::class, 'username')
                ]),
            DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }

    public function updateStatus(string $status, ?User $user = null): void
    {
        if ($user === null && empty($this->getSelectedTableRecordsQuery())) {
            return;
        }

        ($user ?? $this->getSelectedTableRecordsQuery())->update(['status' => $status]);
    }

}
