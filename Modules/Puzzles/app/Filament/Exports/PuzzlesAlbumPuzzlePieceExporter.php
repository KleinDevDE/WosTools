<?php

namespace Modules\Puzzles\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;
use Modules\Puzzles\Models\PuzzlesAlbumPuzzle;

class PuzzlesAlbumPuzzlePieceExporter extends Exporter
{
    protected static ?string $model = PuzzlesAlbumPuzzle::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('puzzle.album.name')->label('Album'),
            ExportColumn::make('puzzle.name')->label('Puzzle'),
            ExportColumn::make('stars')->label('Piece Stars'),
        ];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return parent::modifyQuery($query)->orderBy('position');
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
