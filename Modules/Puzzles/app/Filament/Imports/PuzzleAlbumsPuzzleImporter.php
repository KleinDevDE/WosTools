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
            ImportColumn::make('album.de')->label('Album (Deutsch)'),
            ImportColumn::make('album.en')->label('Album (English)'),
            ImportColumn::make('album.tr')->label('Album (Türkçe)'),
            ImportColumn::make('name.de')->label('Puzzle (Deutsch)'),
            ImportColumn::make('name.en')->label('Puzzle (English)'),
            ImportColumn::make('name.tr')->label('Puzzle (Türkçe)'),
        ];
    }

    public function remapData(): void
    {
        parent::remapData();
        Log::info("Data:", $this->data);

        // Find album by checking all language columns
        $albumName = null;
        $albumLang = null;

        // Check which album column has data
        if (!empty($this->data['album.de'])) {
            $albumName = $this->data['album.de'];
            $albumLang = 'de';
        } elseif (!empty($this->data['album.en'])) {
            $albumName = $this->data['album.en'];
            $albumLang = 'en';
        } elseif (!empty($this->data['album.tr'])) {
            $albumName = $this->data['album.tr'];
            $albumLang = 'tr';
        }

        // Handle JSON format (for exported data)
        if ($albumName && str_starts_with($albumName, '{')) {
            $decoded = json_decode($albumName, true);
            $albumName = $decoded[$albumLang] ?? $decoded['de'] ?? null;
        }

        // Find or create album
        $album = null;
        if ($albumName) {
            // Try to find album by checking translations
            $album = PuzzlesAlbum::all()->first(function ($a) use ($albumName, $albumLang) {
                return $a->getTranslation('name', $albumLang) === $albumName;
            });

            // If not found, create new
            if (!$album) {
                $albumPosition = PuzzlesAlbum::query()->max('position') ?? 0;
                $album = PuzzlesAlbum::query()->create([
                    'name' => [
                        'de' => $albumName,
                        'en' => $albumName,
                        'tr' => $albumName,
                    ],
                    'position' => $albumPosition + 1
                ]);
            }
        }

        Log::info("Album:", $album?->toArray() ?? []);

        // Build puzzle translations
        $puzzleTranslations = [];
        if (!empty($this->data['name.de'])) {
            $puzzleTranslations['de'] = str_replace(["\"", "."], "", $this->data['name.de']);
        }
        if (!empty($this->data['name.en'])) {
            $puzzleTranslations['en'] = str_replace(["\"", "."], "", $this->data['name.en']);
        }
        if (!empty($this->data['name.tr'])) {
            $puzzleTranslations['tr'] = str_replace(["\"", "."], "", $this->data['name.tr']);
        }

        // Fallback if only one language provided
        if (count($puzzleTranslations) === 1) {
            $fallbackValue = reset($puzzleTranslations);
            $puzzleTranslations = [
                'de' => $puzzleTranslations['de'] ?? $fallbackValue,
                'en' => $puzzleTranslations['en'] ?? $fallbackValue,
                'tr' => $puzzleTranslations['tr'] ?? $fallbackValue,
            ];
        }

        $puzzlePosition = PuzzlesAlbumPuzzle::query()
            ->where('puzzles_album_id', $album?->id)
            ->max('position') ?? 0;

        $this->data = [
            'puzzles_album_id' => $album?->id,
            'name' => $puzzleTranslations,
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
