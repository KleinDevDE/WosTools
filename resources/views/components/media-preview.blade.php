<div class="space-y-4">
    <div class="flex justify-center">
        <img src="{{ $media->getUrl() }}"
             alt="{{ $media->getCustomProperty('alt', $media->name) }}"
             class="max-w-full max-h-96 rounded-lg">
    </div>

    <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
            <span class="font-semibold">File Name:</span>
            <span class="text-gray-600">{{ $media->file_name }}</span>
        </div>
        <div>
            <span class="font-semibold">Size:</span>
            <span class="text-gray-600">{{ number_format($media->size / 1024, 2) }} KB</span>
        </div>
        <div>
            <span class="font-semibold">Type:</span>
            <span class="text-gray-600">{{ $media->mime_type }}</span>
        </div>
        <div>
            <span class="font-semibold">Collection:</span>
            <span class="text-gray-600">{{ $media->collection_name }}</span>
        </div>
        <div>
            <span class="font-semibold">Uploaded:</span>
            <span class="text-gray-600">{{ $media->created_at->format('d.m.Y H:i') }}</span>
        </div>
        <div>
            <span class="font-semibold">Last Modified:</span>
            <span class="text-gray-600">{{ $media->updated_at->format('d.m.Y H:i') }}</span>
        </div>
    </div>
</div>
