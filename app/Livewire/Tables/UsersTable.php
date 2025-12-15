<?php

namespace App\Livewire\Tables;

use App\Models\User;
use App\Services\UserInvitationService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
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
                    User::STATUS_INACTIVE => 'Inactive',
                    User::STATUS_PENDING => 'Pending',
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
                ->copyable()->copyMessage("Username copied to clipboard!")
                ->sortable(),
            TextColumn::make('last_login_at', 'last_login_at')
                ->sortable(),
            TextColumn::make('status')
                ->badge()
                ->color(fn(User $record) => match ($record->status) {
                    User::STATUS_ACTIVE => 'success',
                    User::STATUS_INACTIVE => 'danger',
                    User::STATUS_PENDING => 'warning',
                    User::STATUS_INVITED => 'primary',
                    default => 'gray',
                })
                ->sortable(),
        ];
    }

    public function getTableBulkActions(): array
    {
        return [
            BulkAction::make('Update status')
                ->schema([
                    Select::make('status')
                        ->options(function() {
                            return collect(User::STATUS_VALUES)->except(User::STATUS_ACTIVE)
                                ->mapWithKeys(fn($status) => [$status => ucfirst($status)]);
                        }),
                ])
            ->action(function (Collection $records, array $data): void {
                foreach ($records as $record) {
                    $record->update(['status' => $data['status']]);
                }
            }),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Action::make('inviteUser')
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
                                ->dispatch('copy-to-clipboard', ['text' => $invitation->invitationURL, 'element' => "copy-inv-url-$invitation->id"])
                        ])
                        ->send();
                }),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('copy_inv_url')
                //Only if status === invited
                ->hidden(fn(User $user) => $user->status !== User::STATUS_INVITED)
                ->label("Copy Invitation")
                ->button()
                ->icon(Heroicon::Clipboard)
                ->iconPosition(IconPosition::After)
                ->extraAttributes(fn (User $user) => ['id' => "copy-inv-url-$user->id"])
                ->dispatch('copy-to-clipboard', fn (User $user) => [
                    'text' => $user->invitations()->first()?->invitationURL,
                    'element' => "copy-inv-url-$user->id"
                ]),
            Action::make('approve')
                ->label("Approve")
                ->hidden(fn(User $user) => $user->status !== User::STATUS_PENDING)
                ->button()->size(Size::ExtraSmall)->icon(Heroicon::Check)->color('success')
                ->action(fn(User $user) => $this->approveUsers($user)),

            Action::make('reject')
                ->label("Reject")
                ->hidden(fn(User $user) => $user->status !== User::STATUS_PENDING)
                ->button()->size(Size::ExtraSmall)->icon(Heroicon::XMark)->color('danger')
                ->action(fn(User $user) => $this->rejectUsers($user)),
        ];
    }

    public function enableUsers(?User $user = null): void
    {
        //TODO Use Service
        $this->updateStatus(User::STATUS_ACTIVE, $user);
    }

    public function disableUsers(?User $user = null): void
    {
        //TODO Use Service
        $this->updateStatus(User::STATUS_INACTIVE, $user);
    }

    public function approveUsers(?User $user = null): void
    {
        //TODO Use Service
        $this->updateStatus(User::STATUS_ACTIVE, $user);
    }

    public function rejectUsers(?User $user = null): void
    {
        //TODO Use Service
        $this->updateStatus(User::STATUS_INACTIVE, $user);
    }

    public function updateStatus(string $status, ?User $user = null): void
    {
        if ($user === null && empty($this->getSelectedTableRecordsQuery())) {
            return;
        }

        ($user ?? $this->getSelectedTableRecordsQuery())->update(['status' => $status]);
    }

}
