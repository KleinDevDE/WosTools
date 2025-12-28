<?php

namespace Modules\Puzzles\Filament\Imports;

use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Eloquent\Model;
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
            ImportColumn::make('id')->label('ID')->requiredMapping(),
            ImportColumn::make('album_id')->label('Album ID')->requiredMapping(),
            ImportColumn::make('name:de')->label('Puzzle (Deutsch)')->requiredMapping(),
            ImportColumn::make('name:en')->label('Puzzle (English)')->requiredMapping(),
            ImportColumn::make('name:tr')->label('Puzzle (Türkçe)')->requiredMapping(),
        ];
    }

    public function remapData(): void
    {
        parent::remapData();
        $this->data = [
            'id' => $this->data['id'] ?? null,
            'puzzles_album_id' => $this->data['album_id'] ?? null,
            'name' => [
                'de' => $this->data['name:de'] ?? "",
                'en' => $this->data['name:en'] ?? "",
                'tr' => $this->data['name:tr'] ?? "",
            ]
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

        $position = PuzzlesAlbumPuzzle::query()->where('puzzles_album_id', $album->id)->max('position') ?? 0;
        if (empty($this->data['id'])) {
            $puzzle = new PuzzlesAlbumPuzzle([
                'position' => $position + 1
            ]);
            unset($this->data['id']);
        } else {
            $puzzle = PuzzlesAlbumPuzzle::find($this->data['id']);
            if (!$puzzle) {
                throw new RowImportFailedException("No puzzle found with ID [{$this->data['id']}].");
            }
        }

        $puzzle->name = $this->data['name'];

        return $puzzle;
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
