<?php

namespace Modules\Puzzles\Filament\Imports;

use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Modules\Puzzles\Models\PuzzlesAlbum;
use Modules\Puzzles\Models\PuzzlesAlbumPuzzle;

class PuzzleAlbumsPuzzleImporter extends Importer
{
    protected static ?string $model = PuzzlesAlbumPuzzle::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('album')->label('Album'),
            ImportColumn::make('name')->label('Puzzle'),
        ];
    }

    public function resolveRecord(): PuzzlesAlbumPuzzle
    {
        $album = PuzzlesAlbum::query()->firstOrCreate([
            'name' => $this->data['album'],
        ]);

        return PuzzlesAlbumPuzzle::query()->firstOrCreate([
            'puzzles_album_id' => $album->id,
            'name' => $this->data['name'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your puzzle albums puzzle import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
