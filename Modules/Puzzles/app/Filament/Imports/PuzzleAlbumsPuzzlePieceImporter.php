<?php

namespace Modules\Puzzles\Filament\Imports;

use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Eloquent\Model;
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
            ImportColumn::make('id')->label('ID'),
            ImportColumn::make('puzzles_album_id')->label('Album ID'),
            ImportColumn::make('puzzles_album_puzzle_id')->label('Puzzle ID'),
            ImportColumn::make('stars')->label('Piece Stars'),
        ];
    }

    /**
     * @throws RowImportFailedException
     */
    public function resolveRecord(): ?Model
    {
        if (empty($this->data['puzzles_album_id'])) {
            throw new RowImportFailedException("Album ID cannot be empty");
        }

        $album = PuzzlesAlbum::find($this->data['puzzles_album_id']);
        if (!$album) {
            throw new RowImportFailedException("No album found with ID {$this->data['puzzles_album_id']}");
        }

        if (empty($this->data['puzzles_album_puzzle_id'])) {
            throw new RowImportFailedException("Puzzle ID cannot be empty");
        }

        $puzzle = PuzzlesAlbumPuzzle::find($this->data['puzzles_album_puzzle_id']);
        if (!$puzzle) {
            throw new RowImportFailedException("No puzzle found with ID [{$this->data['puzzles_album_puzzle_id']}].");
        }

        $piecePosition = PuzzlesAlbumPuzzlePiece::query()
            ->where('puzzles_album_id', $album->id)
            ->where('puzzles_album_puzzle_id', $puzzle->id)
            ->max('position') ?? 0;
        if (empty($this->data['id'])) {
            $piece = new PuzzlesAlbumPuzzlePiece([
                'position' => $piecePosition + 1,
            ]);
            unset($this->data['id']);
        } else {
            $piece = PuzzlesAlbumPuzzlePiece::find($this->data['id']);
            if (!$piece) {
                throw new RowImportFailedException("No puzzle piece found with ID {$this->data['id']}");
            }
        }

        return $piece;
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
