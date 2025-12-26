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
            ImportColumn::make('name.de')
                ->requiredMapping()
                ->label('Album (Deutsch)'),
            ImportColumn::make('name.en')
                ->label('Album (English)'),
            ImportColumn::make('name.tr')
                ->label('Album (Türkçe)'),
        ];
    }

    public function resolveRecord(): ?Model
    {
        $translations = [];
        if (!empty($this->data['name.de'])) $translations['de'] = $this->data['name.de'];
        if (!empty($this->data['name.en'])) $translations['en'] = $this->data['name.en'];
        if (!empty($this->data['name.tr'])) $translations['tr'] = $this->data['name.tr'];

        // Fallback: If only one language provided, use it for all
        if (count($translations) === 1) {
            $fallback = reset($translations);
            $translations = ['de' => $fallback, 'en' => $fallback, 'tr' => $fallback];
        }

        $albumPosition = PuzzlesAlbum::query()->max('position') ?? 0;
        return PuzzlesAlbum::firstOrNew([
            'name' => $translations,
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
