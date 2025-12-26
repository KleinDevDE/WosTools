<?php

namespace Modules\Puzzles\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;
use Modules\Puzzles\Models\PuzzlesAlbum;

class PuzzlesAlbumExporter extends Exporter
{
    protected static ?string $model = PuzzlesAlbum::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name.de')
                ->label('Album (Deutsch)')
                ->state(fn (PuzzlesAlbum $record) => $record->getTranslation('name', 'de')),
            ExportColumn::make('name.en')
                ->label('Album (English)')
                ->state(fn (PuzzlesAlbum $record) => $record->getTranslation('name', 'en')),
            ExportColumn::make('name.tr')
                ->label('Album (Türkçe)')
                ->state(fn (PuzzlesAlbum $record) => $record->getTranslation('name', 'tr')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your albums puzzle export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
