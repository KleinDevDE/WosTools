<div class="space-y-4">
    <div class="flex justify-center">
        <img src="{{ $media->getTemporaryUrl() }}"
             alt="{{ $media->getCustomProperty('alt', $media->name) }}"
             class="max-w-full max-h-96 rounded-lg">
    </div>

    <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
            <span class="font-semibold text-gray-400 text-sm">File Name</span><br>
            <span class="text-white">{{ $media->file_name }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-400 text-sm">Size</span><br>
            <span class="text-white">{{ number_format($media->size / 1024, 2) }} KB</span>
        </div>
        <div>
            <span class="font-semibold text-gray-400 text-sm">Type</span><br>
            <span class="text-white">{{ $media->mime_type }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-400 text-sm">Collection</span><br>
            <span class="text-white">{{ $media->collection_name }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-400 text-sm">Uploaded</span><br>
            <span class="text-white">{{ $media->created_at->format('d.m.Y H:i') }}</span>
        </div>
        <div>
            <span class="font-semibold text-gray-400 text-sm">Last Modified</span><br>
            <span class="text-white">{{ $media->updated_at->format('d.m.Y H:i') }}</span>
        </div>
    </div>
</div>
