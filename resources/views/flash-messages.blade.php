@if(session('success'))
    <div class="mb-6 rounded-xl bg-green-600/20 border border-green-600/50 p-4 w-fit mx-auto">
        <p class="text-green-400 text-sm font-medium">{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 rounded-xl bg-red-600/20 border border-red-600/50 p-4 w-fit mx-auto">
        <p class="text-red-400 text-sm font-medium">{{ session('error') }}</p>
    </div>
@endif
