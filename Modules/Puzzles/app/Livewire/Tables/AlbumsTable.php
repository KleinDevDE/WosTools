<?php

namespace Modules\Puzzles\Livewire\Tables;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Grouping\Group;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Modules\Puzzles\Models\PuzzlesAlbum;
use Modules\Puzzles\Models\PuzzlesAlbumPuzzle;

class AlbumsTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(PuzzlesAlbumPuzzle::query())
            ->groups([
                Group::make('album.name')
                    ->label('Album: ')
//                    ->getDescriptionFromRecordUsing(
//                        fn (OrderService $record): string
//                        => 'Company: '. $record->order->company->tradename. ' Customer: '. $record->order->customer->name
//                    )
                    ->collapsible()
            ])
            ->defaultGroup('album.name')
            ->selectable();
    }

    protected function getTableFilters(): array
    {
        return [
        ];
    }

    public function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->searchable()
                ->sortable(),
        ];
    }

    public function getTableBulkActions(): array
    {
        return [
            DeleteBulkAction::make(),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->schema([
                Select::make('puzzles_album_id')
                    ->options(PuzzlesAlbum::all()->pluck('name', 'id'))
                    ->label('Album:'),
                TextInput::make('name')
            ])
        ];
    }

    protected function getTableActions(): array
    {
        return [
        ];
    }

    public function render(): View
    {
        return view('puzzles::livewire.tables.albums-table');
    }

}
