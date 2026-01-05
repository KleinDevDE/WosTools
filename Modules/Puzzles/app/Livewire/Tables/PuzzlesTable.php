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
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
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
use Modules\Puzzles\Models\PuzzlesAlbumPuzzlePiece;

/*
 * TODO: Rebuild table
 */
class PuzzlesTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(PuzzlesAlbumPuzzle::query())
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
            SpatieMediaLibraryImageColumn::make('image')
                ->collection('cover')
                ->conversion('thumb')
                ->square()
                ->label('Puzzle Image'),
            TextColumn::make('album.name')
                ->searchable()
                ->sortable(),
            TextColumn::make('name')
                ->searchable()
                ->getStateUsing(fn (PuzzlesAlbumPuzzle $record) =>
                    $record->getTranslation('name', app()->getLocale())
                ),
            //Count pieces
            TextColumn::make('pieces_count')
            ->getStateUsing(fn (PuzzlesAlbumPuzzle $record) => $record->pieces->count())
            ->label('Pieces')
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
                        ->options(PuzzlesAlbum::query()->get()->pluck(function($album) {
                            return $album->getTranslation('name', app()->getLocale());
                        }, 'id'))
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
            CreateAction::make('create')
                ->label('Add Puzzle')
                ->modelLabel('Puzzle')
            ->schema([
                SpatieMediaLibraryFileUpload::make('image')
                    ->collection('cover')
                    ->responsiveImages()
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
                    ->maxSize(5120)
                    ->helperText('Upload an image for this puzzle'),

                Select::make('puzzles_album_id')
                    ->label('Album:')
                    ->options(PuzzlesAlbum::query()->get()->pluck(function($album) {
                        return $album->getTranslation('name', app()->getLocale());
                    }, 'id'))
                    ->required(),

                Tabs::make('translations')
                    ->tabs([
                        Tab::make('Deutsch')
                            ->icon('heroicon-m-language')
                            ->schema([
                                TextInput::make('name.de')
                                    ->label('Name (Deutsch)')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Tab::make('English')
                            ->icon('heroicon-m-language')
                            ->schema([
                                TextInput::make('name.en')
                                    ->label('Name (English)')
                                    ->maxLength(255),
                            ]),
                        Tab::make('Türkçe')
                            ->icon('heroicon-m-language')
                            ->schema([
                                TextInput::make('name.tr')
                                    ->label('Ad (Türkçe)')
                                    ->maxLength(255),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]),
            CreateAction::make('create-multiple')
                ->label('Add Multiple Puzzles')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('image')
                        ->collection('cover')
                        ->responsiveImages()
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
                        ->maxSize(5120)
                        ->helperText('Upload an image for this puzzle'),
                    Select::make('puzzles_album_id')
                        ->options(PuzzlesAlbum::query()->get()->pluck(function($album) {
                            return $album->getTranslation('name', app()->getLocale());
                        }, 'id'))
                        ->label('Album:')
                        ->string()
                        ->required(),
                    Textarea::make('names')
                        ->label('Puzzle-Names (one name per line - German only)')
                        ->rows(5)
                        ->autofocus()
                        ->required()
                        ->string()
                        ->helperText('Empty lines will be ignored. Names will be set in German only.')
                ])
                ->using(function (array $data) {
                    // Zerlegen: je Zeile ein Name, trimmen, Leere/Duplikate entfernen
                    $created = null;
                    $puzzles = collect(preg_split("/\r\n|\r|\n/", (string)$data['names']))
                        ->map(fn($name) => trim($name))
                        ->unique()->filter();

                    $existingPuzzles = PuzzlesAlbumPuzzle::query()
                        ->where('puzzles_album_id', $data['puzzles_album_id'])
                        ->get()
                        ->filter(function($puzzle) use ($puzzles) {
                            // Check if German translation exists in the list
                            return $puzzles->contains($puzzle->getTranslation('name', 'de'));
                        })
                        ->pluck('name')
                        ->map(fn($translations) => is_array($translations) ? $translations['de'] : $translations)
                        ->toArray();

                    $puzzles = $puzzles->diff($existingPuzzles);

                    $puzzles->each(function ($name) use ($data, &$created) {
                        $puzzlePosition = PuzzlesAlbumPuzzle::query()
                            ->where('puzzles_album_id', $data['puzzles_album_id'])
                            ->max('position') ?? 1;

                        $created = PuzzlesAlbumPuzzle::query()->create([
                            'puzzles_album_id' => $data['puzzles_album_id'],
                            'name' => [
                                'de' => $name,
                                'en' => $name, // Initially same as German
                                'tr' => $name, // Initially same as German
                            ],
                            'position' => $puzzlePosition + 1
                        ]);
                    });

                    // Rückgabe eines (beliebigen) angelegten Datensatzes für Filament
                    return $created;
                }),

            ActionGroup::make([
                ExportAction::make("Export")
                    ->label("Export")
                    ->exporter(PuzzlesAlbumPuzzleExporter::class)
                    ->icon(Heroicon::DocumentChartBar),
                ImportAction::make("Import")
                    ->label("Import")
                    ->importer(PuzzleAlbumsPuzzleImporter::class)
                    ->icon(Heroicon::DocumentArrowUp),
            ]),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            EditAction::make('edit-puzzle')
                ->label('Edit Puzzle')
                ->modalWidth('lg')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('image')
                        ->collection('cover')
                        ->responsiveImages()
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
                        ->maxSize(5120)
                        ->helperText('Upload an image for this puzzle'),

                    Select::make('puzzles_album_id')
                        ->label('Album:')
                        ->options(PuzzlesAlbum::query()->get()->pluck(function($album) {
                            return $album->getTranslation('name', app()->getLocale());
                        }, 'id'))
                        ->required()
                        ->string()
                        ->default(function (PuzzlesAlbumPuzzle $record) {
                            return $record->puzzles_album_id;
                        }),

                    TextInput::make('position')
                        ->numeric()
                        ->label('Position'),

                    Tabs::make('translations')
                        ->tabs([
                            Tab::make('Deutsch')
                                ->icon('heroicon-m-language')
                                ->schema([
                                    TextInput::make('name.de')
                                        ->label('Name (Deutsch)')
                                        ->required()
                                        ->maxLength(255),
                                ]),
                            Tab::make('English')
                                ->icon('heroicon-m-language')
                                ->schema([
                                    TextInput::make('name.en')
                                        ->label('Name (English)')
                                        ->maxLength(255),
                                ]),
                            Tab::make('Türkçe')
                                ->icon('heroicon-m-language')
                                ->schema([
                                    TextInput::make('name.tr')
                                        ->label('Ad (Türkçe)')
                                        ->maxLength(255),
                                ]),
                        ])
                        ->columnSpanFull(),
                ]),
            CreateAction::make("add-pieces")
            ->label("Add Puzzle Pieces")
            ->icon(Heroicon::PlusCircle)
                ->schema([
                    Textarea::make('stars')
                        ->label('Puzzle Pieces (write star count per line):')
                        ->rows(5)
                        ->autofocus()
                        ->required()
                        ->string()
                        ->helperText('Empty lines will be ignored.')
                ])
                ->using(function (array $data, PuzzlesAlbumPuzzle $record) {
                    // Zerlegen: je Zeile ein Name, trimmen, Leere/Duplikate entfernen
                    $created = null;
                    $starts = collect(preg_split("/\r\n|\r|\n/", (string)$data['stars']))
                        ->map(fn($name) => trim($name))
                        ->filter();

                    $position = 1;
                    $starts->each(function ($star) use (&$position, $record, $data, &$created) {
                        $created = PuzzlesAlbumPuzzlePiece::query()->create([
                            'puzzles_album_id' => $record->puzzles_album_id,
                            'puzzles_album_puzzle_id' => $record->id,
                            'position' => $position++,
                            'stars' => (int)$star
                        ]);
                    });

                    return $created;
                }),
            DeleteAction::make()
        ];
    }

    public function render(): View
    {
        return view('puzzles::livewire.tables.albums-table');
    }

}
