<?php

namespace Modules\Puzzles\Filament\Imports;

use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Number;
use Modules\Puzzles\Models\PuzzlesAlbum;
use Modules\Puzzles\Models\PuzzlesAlbumPuzzle;
use Modules\Puzzles\Models\PuzzlesAlbumPuzzlePiece;

class PuzzleAlbumsPuzzlePieceImporter extends Importer
{
    protected static ?string $model = PuzzlesAlbumPuzzlePiece::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('album')->label('Album'),
            ImportColumn::make('puzzle')->label('Puzzle'),
            ImportColumn::make('stars')->label('Piece Stars'),
        ];
    }

    public function remapData(): void
    {
        parent::remapData();
        if (str_starts_with($this->data['album'], '{')) {
            $this->data['album'] = json_decode($this->data['album'], true)['name'] ?? null;
        }

        $albumPosition = PuzzlesAlbum::query()->max('position') ?? 0;
        $album = PuzzlesAlbum::query()->firstOrCreate(
            ['name' => $this->data['album']],
            ['position' => $albumPosition + 1]
        );

        $puzzlePosition = PuzzlesAlbumPuzzle::query()
            ->where('puzzles_album_id', $album->id)->max('position') ?? 0;
        $puzzle = PuzzlesAlbumPuzzle::query()->firstOrCreate(
            [
                'puzzles_album_id' => $album->id,
                'name' => $this->data['puzzle'],
            ],
            [
                'position' => $puzzlePosition + 1,
            ]
        );

        $piecePosition = PuzzlesAlbumPuzzlePiece::query()
            ->where('puzzles_album_id', $album->id)
            ->where('puzzles_album_puzzle_id', $puzzle->id)
            ->max('position') ?? 0;
        $this->data = [
            'puzzles_album_id' => $album->id,
            'puzzles_album_puzzle_id' => $puzzle->id,
            'stars' => $this->data['stars'],
            'position' => $piecePosition + 1
        ];
    }

    public function resolveRecord(): ?PuzzlesAlbumPuzzlePiece
    {
        return PuzzlesAlbumPuzzlePiece::query()->firstOrNew(
            [
                'puzzles_album_id' => $this->data['puzzles_album_id'],
                'puzzles_album_puzzle_id' => $this->data['puzzles_album_puzzle_id'],
                'position' => $this->data['position']
            ],
            [
                'stars' => $this->data['stars'],
            ]
        );
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your puzzle albums puzzle import has completed and ' . Number::format(
                $import->successful_rows
            ) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural(
                    $failedRowsCount
                ) . ' failed to import.';
        }

        return $body;
    }
}
