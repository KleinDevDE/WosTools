<?php

namespace Modules\Puzzles\Livewire\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Grouping\Group;
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
use Modules\Puzzles\Filament\Exports\PuzzlesAlbumPuzzleExporter;
use Modules\Puzzles\Filament\Imports\PuzzleAlbumsPuzzleImporter;
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
            ->paginationPageOptions([50, 100, 200])
            ->groups([
                Group::make('album.name')
                    ->label('Album: ')
                    ->getDescriptionFromRecordUsing(
                        fn(PuzzlesAlbumPuzzle $record): string => 'Anzahl Puzzles: ' . $record->album->puzzles->count()
                    )
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
            TextColumn::make('id')
                ->sortable()
                ->label(""),
            TextColumn::make('name')
                ->searchable()
                ->sortable(),
        ];
    }

    public function getTableBulkActions(): array
    {
        return [
            DeleteBulkAction::make(),
            //Open Form, choose album, change puzzles_album_id of the recods
            BulkAction::make('Update to album')
                ->icon(Heroicon::ArrowRightEndOnRectangle)
                ->schema([
                    Select::make('puzzles_album_id')
                        ->options(PuzzlesAlbum::query()->pluck('name', 'id'))
                        ->label('Album:')
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
                ->exporter(PuzzlesAlbumPuzzleExporter::class)
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Mehrere Puzzles anlegen')
                ->schema([
                    Select::make('puzzles_album_id')
                        ->options(PuzzlesAlbum::query()->pluck('name', 'id'))
                        ->label('Album:')
                        ->string()
                        ->required(),
                    Textarea::make('names')
                        ->label('Puzzle-Namen (je Zeile ein Name)')
                        ->rows(5)
                        ->autofocus()
                        ->required()
                        ->string()
                        ->helperText('Leere/doppelte Zeilen werden ignoriert.')
                ])
                ->using(function (array $data) {
                    // Zerlegen: je Zeile ein Name, trimmen, Leere/Duplikate entfernen
                    $created = null;
                    $puzzles = collect(preg_split("/\r\n|\r|\n/", (string)$data['names']))
                        ->map(fn($name) => trim($name))
                        ->unique()->filter();

                    $existingPuzzles = PuzzlesAlbumPuzzle::query()
                        ->where('puzzles_album_id', $data['puzzles_album_id'])
                        ->whereIn('name', $puzzles)
                        ->pluck('name')->toArray();
                    $puzzles = $puzzles->diff($existingPuzzles);
                    debugbar()->debug($puzzles);
                    $puzzles->each(function ($name) use ($data, &$created) {
                        $created = PuzzlesAlbumPuzzle::query()->create([
                            'puzzles_album_id' => $data['puzzles_album_id'],
                            'name' => $name,
                        ]);
                    });

                    // Rückgabe eines (beliebigen) angelegten Datensatzes für Filament
                    return $created;
                }),
            ImportAction::make("Import from Excel")
            ->importer(PuzzleAlbumsPuzzleImporter::class)
            ->icon(Heroicon::DocumentArrowUp),
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
