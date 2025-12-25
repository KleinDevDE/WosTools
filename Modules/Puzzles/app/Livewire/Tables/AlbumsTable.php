<?php

namespace Modules\Puzzles\Livewire\Tables;

use App\Helpers\Permissions;
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
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
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
use Modules\Puzzles\Filament\Exports\PuzzlesAlbumExporter;
use Modules\Puzzles\Filament\Exports\PuzzlesAlbumPuzzleExporter;
use Modules\Puzzles\Filament\Imports\PuzzleAlbumsImporter;
use Modules\Puzzles\Filament\Imports\PuzzleAlbumsPuzzleImporter;
use Modules\Puzzles\Models\PuzzlesAlbum;
use Modules\Puzzles\Models\PuzzlesAlbumPuzzle;
use Modules\Puzzles\Models\PuzzlesAlbumPuzzlePiece;

class AlbumsTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(PuzzlesAlbum::query())
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
            SpatieMediaLibraryImageColumn::make('cover')
//                ->getStateUsing(fn (PuzzlesAlbum $record) => $record->getFirstMediaUrl('cover', 'thumb'))
//                ->size(60)
                    ->collection('cover')
//                ->conversion('thumb')
                ->label('Album Cover'),
            TextColumn::make('position')
                ->sortable()
                ->label(""),
            TextColumn::make('name')
                ->searchable()
                ->sortable(),
        ];
    }

    public function getTableBulkActions(): array
    {
        $actions = [];

        $actions[] = ExportBulkAction::make("Export to Excel")
            ->icon(Heroicon::DocumentChartBar)
            ->exporter(PuzzlesAlbumExporter::class);

        if (auth()->user()->can(Permissions::PUZZLES_ALBUMS_EDIT)) {
            $actions[] = BulkAction::make('Update to album')
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
                });
        }
        if (auth()->user()->can(Permissions::PUZZLES_ALBUMS_DELETE)) {
            $actions[] = DeleteBulkAction::make();
        }

        return $actions;
    }

    protected function getTableHeaderActions(): array
    {
        $actions = [];

        if (auth()->user()->can(Permissions::PUZZLES_ALBUMS_CREATE)) {
            $actions[] = CreateAction::make()
                ->label('Add New Album')
                ->schema([
                    TextInput::make('name')
                ]);
        }

//        if (auth()->user()->can(Permissions::PUZZLES_PUZZLES_CREATE)) {
//            $actions[] = CreateAction::make()
//                ->label('Add Multiple Puzzles')
//                ->schema([
//                    Select::make('puzzles_album_id')
//                        ->options(PuzzlesAlbum::query()->pluck('name', 'id'))
//                        ->label('Album:')
//                        ->string()
//                        ->required(),
//                    Textarea::make('names')
//                        ->label('Puzzle-Names (one name per line)')
//                        ->rows(5)
//                        ->autofocus()
//                        ->required()
//                        ->string()
//                        ->helperText('Empty lines will be ignored.')
//                ])
//                ->using(function (array $data) {
//                    // Zerlegen: je Zeile ein Name, trimmen, Leere/Duplikate entfernen
//                    $created = null;
//                    $puzzles = collect(preg_split("/\r\n|\r|\n/", (string)$data['names']))
//                        ->map(fn($name) => trim($name))
//                        ->unique()->filter();
//
//                    $existingPuzzles = PuzzlesAlbumPuzzle::query()
//                        ->where('puzzles_album_id', $data['puzzles_album_id'])
//                        ->whereIn('name', $puzzles)
//                        ->pluck('name')->toArray();
//                    $puzzles = $puzzles->diff($existingPuzzles);
//                    $puzzles->each(function ($name) use ($data, &$created) {
//                        $puzzlePosition = PuzzlesAlbumPuzzle::query()->where('puzzles_album_id', $data['puzzles_album_id'])->max('position') ?? 1;
//                        $created = PuzzlesAlbumPuzzle::query()->create([
//                            'puzzles_album_id' => $data['puzzles_album_id'],
//                            'name' => $name,
//                            'position' => $puzzlePosition + 1
//                        ]);
//                    });
//
//                    // Rückgabe eines (beliebigen) angelegten Datensatzes für Filament
//                    return $created;
//                });
//        }

        $groupActions = [];
        $groupActions[] = ExportAction::make("Export")
            ->label("Export")
            ->exporter(PuzzlesAlbumExporter::class)
            ->icon(Heroicon::DocumentChartBar);

        if (auth()->user()->can(Permissions::PUZZLES_PUZZLES_CREATE)) {
            $groupActions[] = ImportAction::make("Import")
                ->label("Import")
                ->importer(PuzzleAlbumsImporter::class)
                ->icon(Heroicon::DocumentArrowUp);
            }

        $actions[] = ActionGroup::make($groupActions);

        return $actions;
    }

    protected function getTableActions(): array
    {
        $actions = [];
        if (auth()->user()->can(Permissions::PUZZLES_ALBUMS_EDIT)) {
            $actions[] = EditAction::make('edit-album-cover')
                ->label('Album Cover')
                ->icon(Heroicon::Photo)
                ->modalHeading(fn (PuzzlesAlbum $record) => 'Edit Cover: ' . $record->name)
                ->modalWidth('md')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('cover')
                        ->collection('cover')
                        ->responsiveImages()
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
                        ->maxSize(5120)
                        ->helperText('Upload a cover image for this album'),
                ]);
            $actions[] = EditAction::make();
        }
        if (auth()->user()->can(Permissions::PUZZLES_ALBUMS_DELETE)) {
            $actions[] = DeleteAction::make();
        }
//        if (auth()->user()->can(Permissions::PUZZLES_PIECES_CREATE)) {
//            $actions[] = CreateAction::make("add-pieces")
//                ->label("Add Puzzle Pieces")
//                ->icon(Heroicon::PlusCircle)
//                ->schema([
//                    Textarea::make('stars')
//                        ->label('Puzzle Pieces (write star count per line):')
//                        ->rows(5)
//                        ->autofocus()
//                        ->required()
//                        ->string()
//                        ->helperText('Empty lines will be ignored.')
//                ])
//                ->using(function (array $data, PuzzlesAlbumPuzzle $record) {
//                    // Zerlegen: je Zeile ein Name, trimmen, Leere/Duplikate entfernen
//                    $created = null;
//                    $starts = collect(preg_split("/\r\n|\r|\n/", (string)$data['stars']))
//                        ->map(fn($name) => trim($name))
//                        ->filter();
//
//                    $position = 1;
//                    $starts->each(function ($star) use (&$position, $record, $data, &$created) {
//                        $created = PuzzlesAlbumPuzzlePiece::query()->create([
//                            'puzzles_album_id' => $record->puzzles_album_id,
//                            'puzzles_album_puzzle_id' => $record->id,
//                            'position' => $position++,
//                            'stars' => (int)$star
//                        ]);
//                    });
//
//                    return $created;
//                });
//        }

        return $actions;
    }

    public function render(): View
    {
        return view('puzzles::livewire.tables.albums-table');
    }

}
