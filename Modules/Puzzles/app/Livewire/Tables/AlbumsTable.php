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
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
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
                ->sortable()
                ->getStateUsing(fn (PuzzlesAlbum $record) =>
                    $record->getTranslation('name', app()->getLocale())
                ),
        ];
    }

    public function getTableBulkActions(): array
    {
        $actions = [];

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
                    SpatieMediaLibraryFileUpload::make('cover')
                        ->collection('cover')
                        ->responsiveImages()
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
                        ->maxSize(5120)
                        ->helperText('Upload a cover image for this album'),

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
                ]);
        }

        $groupActions = [];
        $groupActions[] = ExportAction::make("Export")
            ->label("Export")
            ->exporter(PuzzlesAlbumExporter::class)
            ->columnMapping(false)
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
            $actions[] = EditAction::make()
                ->schema([
                    SpatieMediaLibraryFileUpload::make('cover')
                        ->collection('cover')
                        ->responsiveImages()
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
                        ->maxSize(5120)
                        ->helperText('Upload a cover image for this album'),

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
                ]);
        }

        if (auth()->user()->can(Permissions::PUZZLES_ALBUMS_DELETE)) {
            $actions[] = DeleteAction::make();
        }

        return $actions;
    }

    public function render(): View
    {
        return view('puzzles::livewire.tables.albums-table');
    }
}
