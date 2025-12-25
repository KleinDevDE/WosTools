<?php

namespace Modules\Puzzles\Filament\Imports;

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
            ImportColumn::make('name')
                ->requiredMapping()
                ->label('Album')
        ];
    }

    public function resolveRecord(): ?Model
    {
        $albumPosition = PuzzlesAlbum::query()->max('position') ?? 0;
        return PuzzlesAlbum::firstOrNew([
            'name' => $this->data['name'],
            'position' => $albumPosition + 1
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
