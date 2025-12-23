<div x-data="{ open: false }" class="relative">
    <button
        @click="open = !open"
        class="flex items-center gap-2 px-3 py-2 rounded-md hover:bg-navy-700"
        title="{{ __('navigation.language') }}"
    >
        <x-heroicon-o-language class="w-5 h-5"/>
        <span class="text-sm font-medium uppercase">
            {{ strtoupper(app()->getLocale()) }}
        </span>
        <x-heroicon-o-chevron-down class="w-4 h-4"/>
    </button>

    <div
        x-show="open"
        @click.outside="open = false"
        x-transition
        class="absolute right-0 mt-2 w-44 rounded-md bg-navy-750 border border-navy-600 shadow-lg"
    >
        @php
            $languages = [
                'en' => ['name' => 'English', 'flag' => '🇬🇧'],
                'de' => ['name' => 'Deutsch', 'flag' => '🇩🇪'],
                'tr' => ['name' => 'Türkçe', 'flag' => '🇹🇷'],
            ];
            $currentLocale = app()->getLocale();
        @endphp

        @foreach($languages as $code => $lang)
            <form method="POST" action="{{ route('locale.switch') }}" class="w-full">
                @csrf
                <input type="hidden" name="locale" value="{{ $code }}">
                <button
                    type="submit"
                    class="w-full text-left px-4 py-2 text-sm hover:bg-navy-700 flex items-center gap-2
                        {{ $currentLocale === $code ? 'bg-navy-700 text-sky-400' : 'text-slate-300' }}"
                    @click="open = false"
                >
                    <span class="text-lg">{{ $lang['flag'] }}</span>
                    <span>{{ $lang['name'] }}</span>
                    @if($currentLocale === $code)
                        <x-heroicon-o-check class="w-4 h-4 ml-auto text-sky-400"/>
                    @endif
                </button>
            </form>
        @endforeach

        <script>
            // Store locale in localStorage for Vue.js SPA
            document.addEventListener('DOMContentLoaded', function() {
                const currentLocale = '{{ app()->getLocale() }}';
                localStorage.setItem('locale', currentLocale);
            });
        </script>
    </div>
</div>