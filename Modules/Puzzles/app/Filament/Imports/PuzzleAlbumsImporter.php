<?php

namespace Modules\Puzzles\Filament\Imports;

use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Modules\Puzzles\Models\PuzzlesAlbum;

class PuzzleAlbumsImporter extends Importer
{
    protected static ?string $model = PuzzlesAlbum::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('id')
                ->requiredMapping()
                ->label('ID'),
            ImportColumn::make('name:de')
                ->requiredMapping()
                ->label('Album (Deutsch)'),
            ImportColumn::make('name:en')
                ->requiredMapping()
                ->label('Album (English)'),
            ImportColumn::make('name:tr')
                ->requiredMapping()
                ->label('Album (Türkçe)'),
        ];
    }

    public function remapData(): void
    {
        parent::remapData();
        $this->data = [
            'id' => $this->data['id'] ?? null,
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
        $albumPosition = PuzzlesAlbum::query()->max('position') ?? 0;
        if (empty($this->data['id'])) {
            $album = new PuzzlesAlbum([
                'position' => $albumPosition + 1
            ]);
        } else {
            $album = PuzzlesAlbum::find($this->data['id']);
            if (!$album) {
                throw new RowImportFailedException("No album found with ID [{$this->data['id']}].");
            }

            $album->name = $this->data['name'];
        }

        return $album;
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
