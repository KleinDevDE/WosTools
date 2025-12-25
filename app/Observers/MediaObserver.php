<?php

namespace App\Observers;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaObserver
{
    public function creating(Media $media): void
    {
        if (auth()->check()) {
            $media->setCustomProperty('uploaded_by', auth()->id());
            $media->setCustomProperty('uploaded_by_username', auth()->user()->username);
        }
    }

    public function updating(Media $media): void
    {
        if (auth()->check()) {
            $media->setCustomProperty('last_modified_by', auth()->id());
            $media->setCustomProperty('last_modified_by_username', auth()->user()->username);
        }
    }
}
