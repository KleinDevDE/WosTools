<x-auth.layout>
    <div class="space-y-6">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-navy-100 mb-2">Select Your Character</h2>
            <p class="text-navy-300">Choose a character to continue</p>
        </div>

        @error('character')
            <div class="p-4 rounded-xl bg-red-900/20 border border-red-500/50">
                <p class="text-sm text-red-400">{{ $message }}</p>
            </div>
        @enderror

        <div class="space-y-3">
            @foreach($characters as $character)
                <a
                    href="{{ route('character.select.process', $character->id) }}"
                    class="block group"
                >
                    <div class="p-4 rounded-xl border border-white/10 bg-navy-800 hover:bg-navy-700 hover:border-glow-400/50 transition-all duration-200 cursor-pointer">
                        <div class="flex items-center gap-4">
                            {{-- Character Info --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-lg font-semibold text-navy-50 group-hover:text-glow-400 transition">
                                        {{ $character->player_name }}
                                    </h3>
                                    <span class="text-sm text-navy-400">
                                        #{{ $character->player_id }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-4 text-sm text-navy-300">
                                    @if($character->stateRelation)
                                        <div class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                            </svg>
                                            <span>State {{ $character->state }}</span>
                                        </div>
                                    @endif

                                    @if($character->alliance)
                                        <div class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                            </svg>
                                            <span>{{ $character->alliance->name }}</span>
                                        </div>
                                    @endif

                                    @if($character->roles->isNotEmpty())
                                        <div class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                            </svg>
                                            <span>{{ $character->roles->pluck('name')->join(', ') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Arrow Icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-navy-400 group-hover:text-glow-400 group-hover:translate-x-1 transition-all">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="pt-4 border-t border-white/10">
            <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button
                    type="submit"
                    class="w-full text-center text-navy-400 hover:text-navy-200 transition text-sm"
                >
                    Cancel & Logout
                </button>
            </form>
        </div>
    </div>
</x-auth.layout>
