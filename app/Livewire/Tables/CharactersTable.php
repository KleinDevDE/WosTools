<?php

namespace App\Livewire\Tables;

use App\Models\Character;
use Bouncer;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Silber\Bouncer\Database\Role;

class CharactersTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Character::query()->with(['user', 'stateRelation', 'alliance']))
            ->selectable();
    }

    public function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->sortable(),
            TextColumn::make('player_id')
                ->searchable()
                ->iconPosition(IconPosition::After)
                ->icon('heroicon-o-clipboard')
                ->copyable()
                ->copyMessageDuration(500)
                ->sortable(),
            TextColumn::make('player_name')
                ->searchable()
                ->sortable(),
            TextColumn::make('user.name')
                ->label('User')
                ->searchable()
                ->sortable(),
            TextColumn::make('stateRelation.name')
                ->label('State')
                ->placeholder('—')
                ->sortable(),
            TextColumn::make('alliance.name')
                ->label('Alliance')
                ->placeholder('—')
                ->sortable(),
            TextColumn::make('roles')
                ->label('Roles')
                ->getStateUsing(fn(Character $record) => $record->roles->pluck('name')->join(', '))
                ->placeholder('—'),
        ];
    }

    protected function getTableActions(): array
    {
        $availableRoles = Role::all()
            ->mapWithKeys(function(Role $role) {
                return [$role->name => ucfirst($role->name)];
            })->toArray();

        return [
            EditAction::make('edit')
                ->modalHeading('Edit Character Roles')
                ->schema([
                    Select::make('roles')
                        ->label('Roles')
                        ->multiple()
                        ->options($availableRoles)
                        ->afterStateHydrated(function (Select $component, Character $character) {
                            $component->state($character->roles->pluck('name')->toArray());
                        })
                ])
                ->action(function (Character $character, array $data): void {
                    $currentRoles = $character->roles->pluck('name')->toArray();
                    $newRoles = $data['roles'] ?? [];

                    // Sync roles using Bouncer
                    Bouncer::sync($character)->roles($newRoles);

                    // Refresh the character model to get updated roles
                    $character->refresh();

                    Log::channel("audit")->info(
                        "Actor: " . auth('character')->user()?->getName() . " (" . auth('character')->id() . ") | " .
                        "Changed roles of character " . $character->getName() . " from [" . implode(", ", $currentRoles) . "] to [" . implode(", ", $newRoles) . "]"
                    );

                    Notification::make()
                        ->title('Roles updated successfully')
                        ->success()
                        ->send();
                }),

            Action::make('transfer_r5')
                ->label('Transfer R5')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn(Character $character) =>
                    auth('character')->user()?->isA('wos_r5') &&
                    !$character->isA('wos_r5')
                )
                ->requiresConfirmation()
                ->modalHeading('Transfer R5 Role')
                ->modalDescription(fn(Character $character) =>
                    "You are about to transfer your R5 role to {$character->player_name}. This action will remove your R5 role and grant it to the selected character."
                )
                ->action(function (Character $character): void {
                    $currentR5 = auth('character')->user();

                    if (!$currentR5) {
                        Notification::make()
                            ->title('Error: Not authenticated')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Remove R5 from current character
                    Bouncer::retract('wos_r5')->from($currentR5);

                    // Add R5 to new character
                    Bouncer::assign('wos_r5')->to($character);

                    // Also ensure they have R4 role
                    if (!$character->isA('wos_r4')) {
                        Bouncer::assign('wos_r4')->to($character);
                    }

                    Log::channel("audit")->info(
                        "Actor: " . $currentR5->getName() . " (" . $currentR5->id . ") | " .
                        "Transferred R5 role to character " . $character->getName() . " (" . $character->id . ")"
                    );

                    Notification::make()
                        ->title('R5 role transferred successfully')
                        ->success()
                        ->body("The R5 role has been transferred to {$character->player_name}")
                        ->send();
                }),

            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Delete Character')
                ->modalDescription(fn(Character $character) =>
                    "Are you sure you want to delete the character '{$character->player_name}'? This action cannot be undone."
                )
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Character deleted')
                        ->body('The character has been deleted successfully.')
                ),
        ];
    }

    public function render()
    {
        return view('livewire.tables.characters-table');
    }
}
