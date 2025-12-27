<?php

namespace Modules\Puzzles\Livewire\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
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
use Modules\Puzzles\Filament\Exports\PuzzlesAlbumPuzzlePieceExporter;
use Modules\Puzzles\Filament\Imports\PuzzleAlbumsPuzzlePieceImporter;
use Modules\Puzzles\Models\PuzzlesAlbum;
use Modules\Puzzles\Models\PuzzlesAlbumPuzzle;
use Modules\Puzzles\Models\PuzzlesAlbumPuzzlePiece;

/*
* TODO: Rebuild table
*/
class PuzzlesPiecesTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(PuzzlesAlbumPuzzlePiece::query())
            ->paginationPageOptions([50, 100, 200])
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
            TextColumn::make('position')
                ->sortable()
                ->label(""),
            TextColumn::make('stars')
                ->sortable()
                ->label("Stars"),
        ];
    }

    public function getTableBulkActions(): array
    {
        return [
            DeleteBulkAction::make(),
            //Open Form, choose album, change puzzles_album_id of the recods
            BulkAction::make('Move to album')
                ->icon(Heroicon::ArrowRightEndOnRectangle)
                ->schema([
                    Select::make('puzzles_album_id')
                        ->options(PuzzlesAlbum::query()->pluck('name', 'id'))
                        ->label('Album')
                        ->string()
                        ->required(),
                ])
                ->action(function (array $data, Collection $records) {
                    foreach ($records as $record) {
                        $record->puzzles_album_id = $data['puzzles_album_id'];
                        $record->save();
                    }
                }),
            BulkAction::make('Move to puzzle')
                ->icon(Heroicon::ArrowRightEndOnRectangle)
                ->schema([
                    Select::make('puzzles_album_puzzle_id')
                        ->options(PuzzlesAlbumPuzzle::query()->pluck('name', 'id'))
                        ->label('Puzzle')
                        ->string()
                        ->required(),
                ])
                ->action(function (array $data, Collection $records) {
                    foreach ($records as $record) {
                        $record->puzzles_album_id = $data['puzzles_album_id'];
                        $record->save();
                    }
                }),
            ExportBulkAction::make("Export to Excel")
                ->icon(Heroicon::DocumentChartBar)
                ->exporter(PuzzlesAlbumPuzzlePieceExporter::class)
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add Piece')
                ->modalWidth('md')
                ->mutateDataUsing(function (array $data) {
                    $position = PuzzlesAlbumPuzzlePiece::query()
                            ->where('puzzles_album_id', $data['puzzles_album_id'])
                            ->where('puzzles_album_puzzle_id', $data['puzzles_album_puzzle_id'])
                            ->max('position') + 1;

                    return $data + ['position' => $position];
                })
                ->schema([
                    Select::make('puzzles_album_id')
                        ->label('Album')
                        ->options(PuzzlesAlbum::query()->pluck('name', 'id'))
                        ->required()->string(),
                    Select::make('puzzles_album_puzzle_id')
                        ->label('Puzzle')
                        ->options(PuzzlesAlbumPuzzle::query()->pluck('name', 'id'))
                        ->required()->string(),
                    TextInput::make('stars')->numeric()->required(),
                ]),
            ActionGroup::make([
                ExportAction::make("Export")
                    ->label("Export")
                    ->exporter(PuzzlesAlbumPuzzlePieceExporter::class)
                    ->icon(Heroicon::DocumentChartBar),
                ImportAction::make("Import")
                    ->label("Import")
                    ->importer(PuzzleAlbumsPuzzlePieceImporter::class)
                    ->icon(Heroicon::DocumentArrowUp),
            ]),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            EditAction::make('edit-piece-image')
                ->modalWidth('md')
                ->schema([
                    Select::make('puzzles_album_id')
                        ->label('Album')
                        ->required()
                        ->options(PuzzlesAlbum::query()->pluck('name', 'id'))
                        ->required()->string()->default(function (PuzzlesAlbumPuzzle $record) {
                            return $record->puzzles_album_id;
                        }),
                    Select::make('puzzles_album_puzzle_id')
                        ->label('Puzzle')
                        ->required()
                        ->options(PuzzlesAlbumPuzzle::query()->pluck('name', 'id'))
                        ->required()->string()->default(function (PuzzlesAlbumPuzzlePiece $record) {
                            return $record->puzzles_album_puzzle_id;
                        }),
                    TextInput::make('stars')->numeric()->required()
                ]),
            DeleteAction::make()
        ];
    }

    public function render(): View
    {
        return view('puzzles::livewire.tables.albums-table');
    }

}
