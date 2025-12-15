<?php

namespace App\Livewire\Tables;

use App\Models\User;
use App\Services\UserInvitationService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
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
            Action::make('edit')
                ->url(fn(User $user): string => route('users.list', $user))
                ->openUrlInNewTab(),
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

    public function acceptUsers(?User $user = null): void
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

        $this->getSelectedTableRecordsQuery()->update(['status' => $status]);
    }

}
