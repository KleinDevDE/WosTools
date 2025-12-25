<?php

namespace App\Livewire\Tables;

use App\Helpers\Permissions;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaGalleryTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getMediaQuery())
            ->paginationPageOptions([25, 50, 100])
            ->defaultSort('created_at', 'desc')
            ->selectable();
    }

    protected function getMediaQuery(): Builder
    {
        $query = Media::query();

        // Apply permission-based filtering
        // Only show media the user has rights to view
        $user = auth()->user();

        if (!$user->can(Permissions::MEDIA_GALLERY_VIEW)) {
            // If no gallery view permission, return empty
            return $query->whereRaw('1 = 0');
        }

        // Filter based on model permissions
        $query->where(function ($q) use ($user) {
            // Show Puzzles media if user can view puzzles
            if ($user->can(Permissions::PUZZLES_VIEW)) {
                $q->orWhere('model_type', 'like', '%Puzzles%');
            }
            // Add other model types as needed
        });

        return $query;
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('collection_name')
                ->options([
                    'cover' => 'Album Covers',
                    'image' => 'Images',
                ])
                ->label('Collection'),
            SelectFilter::make('model_type')
                ->options([
                    'Modules\\Puzzles\\Models\\PuzzlesAlbum' => 'Albums',
                    'Modules\\Puzzles\\Models\\PuzzlesAlbumPuzzle' => 'Puzzles',
                    'Modules\\Puzzles\\Models\\PuzzlesAlbumPuzzlePiece' => 'Pieces',
                ])
                ->label('Model Type'),
        ];
    }

    public function getTableColumns(): array
    {
        return [
            ImageColumn::make('preview')
                ->getStateUsing(fn (Media $record) => $record->getTemporaryUrl())
//                ->size(80)
//                ->square()
                ->label('Preview'),
            TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->copyable()
                ->label('Name'),
            TextColumn::make('file_name')
                ->searchable()
                ->sortable()
                ->label('File Name'),
            TextColumn::make('collection_name')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'cover' => 'success',
                    'image' => 'info',
                    default => 'gray',
                })
                ->sortable()
                ->label('Collection'),
            TextColumn::make('model_type')
                ->formatStateUsing(fn (string $state): string => class_basename($state))
                ->badge()
                ->sortable()
                ->label('Model'),
            TextColumn::make('size')
                ->formatStateUsing(fn (int $state): string => number_format($state / 1024, 2) . ' KB')
                ->sortable()
                ->label('Size'),
            TextColumn::make('mime_type')
                ->label('Type')
                ->sortable(),
            TextColumn::make('uploader')
                ->getStateUsing(fn (Media $record) => $record->getCustomProperty('uploaded_by_username', 'Unknown'))
                ->label('Uploaded By')
                ->searchable(),
            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->label('Uploaded At'),
            TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->label('Last Modified'),
        ];
    }

    public function getTableBulkActions(): array
    {
        $actions = [];

        if (auth()->user()->can(Permissions::MEDIA_GALLERY_DELETE)) {
            $actions[] = DeleteBulkAction::make()
                ->requiresConfirmation()
                ->modalHeading('Delete Media Files')
                ->modalDescription('Are you sure you want to delete these media files? This action cannot be undone.')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Media deleted')
                        ->body('Selected media files have been deleted.')
                );
        }

        return $actions;
    }

    protected function getTableActions(): array
    {
        $actions = [];

        if (auth()->user()->can(Permissions::MEDIA_GALLERY_EDIT)) {
            $actions[] = EditAction::make('edit')
                ->label('Edit')
                ->icon(Heroicon::PencilSquare)
                ->modalHeading('Edit Media')
                ->modalWidth('md')
                ->form([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->helperText('The name/title of this media file'),
                    TextInput::make('custom_properties.alt')
                        ->label('Alt Text')
                        ->maxLength(255)
                        ->helperText('Alternative text for accessibility'),
                ])
                ->mutateRecordDataUsing(function (array $data, Media $record): array {
                    $data['custom_properties.alt'] = $record->getCustomProperty('alt', '');
                    return $data;
                })
                ->using(function (Media $record, array $data): Media {
                    $record->name = $data['name'];
                    $record->setCustomProperty('alt', $data['custom_properties.alt'] ?? '');
                    $record->save();
                    return $record;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Media updated')
                        ->body('Media information has been updated.')
                );
        }

        $actions[] = Action::make('view')
            ->label('View')
            ->icon(Heroicon::Eye)
            ->modalHeading(fn (Media $record) => $record->name)
            ->modalContent(fn (Media $record) => view('components.media-preview', ['media' => $record]))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');

        if (auth()->user()->can(Permissions::MEDIA_GALLERY_DELETE)) {
            $actions[] = Action::make('delete')
                ->label('Delete')
                ->icon(Heroicon::Trash)
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (Media $record) {
                    $record->delete();
                    Notification::make()
                        ->success()
                        ->title('Media deleted')
                        ->send();
                });
        }

        return $actions;
    }

    public function render(): View
    {
        return view('livewire.tables.media-gallery-table');
    }
}
