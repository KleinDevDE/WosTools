<?php

namespace Modules\Puzzles\Filament\Imports;

use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;
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

    public function remapData(): void
    {
        parent::remapData();
        Log::info("Data:", $this->data);
        if (str_starts_with($this->data['album'], '{')) {
            $this->data['album'] = json_decode($this->data['album'], true)['name'] ?? null;
        }

        $albumPosition = PuzzlesAlbum::query()->max('position') ?? 1;
        $album = PuzzlesAlbum::query()->firstOrCreate(
            ['name' => $this->data['album']],
            ['position' => $albumPosition + 1]
        );
        Log::info("Album:", $album->toArray());

        $puzzlePosition = PuzzlesAlbumPuzzle::query()->where('puzzles_album_id', $album->id)->max('position') ?? 1;
        $this->data = [
            'puzzles_album_id' => $album->id,
            'name' => str_replace(["\"", "."], "", $this->data['name']),
            'position' => $puzzlePosition + 1
        ];
        Log::info("Data:", $this->data);
    }

    public function resolveRecord(): ?PuzzlesAlbumPuzzle
    {
        return PuzzlesAlbumPuzzle::query()->firstOrNew(
            [
                'puzzles_album_id' => $this->data['puzzles_album_id'],
                'name' => $this->data['name'],
            ],
            [
                'position' => $this->data['position'],
            ]
        );
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
