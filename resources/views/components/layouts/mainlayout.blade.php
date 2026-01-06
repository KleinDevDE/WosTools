<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Whiteout Survival</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">


        @vite(['resources/css/app.css'])
        @livewireStyles
        @filamentStyles
    </head>

    <body class="min-h-screen bg-navy-900 text-navy-200">

        @php
            /**
             * Active state helper.
             * Usage: $isActive('route.name.*')
             */
            $isActive = fn ($routes) =>
                request()->routeIs($routes)
                    ? 'text-sky-400 border-b-2 border-sky-400'
                    : 'text-slate-400 hover:text-slate-200';
        @endphp

        {{-- Header --}}
        <nav class="fixed top-0 z-20 w-full border-b border-navy-700 bg-navy-700">
            <div class="flex flex-wrap items-center justify-between px-4 py-2 md:py-0 md:h-15.25">
                {{-- Branding --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 order-1">
                    <x-heroicon-o-sparkles class="h-6 w-6 text-sky-400"/>
                    <span class="whitespace-nowrap text-lg font-semibold">WoSTools</span>
                </a>

                {{-- Navigation--}}
                <ul class="order-last flex w-full items-center justify-center gap-6 py-2 text-sm font-medium md:order-0 md:w-auto md:border-0 md:py-0 order-3 md:order-2">
                    <li>
                        <a href="{{ route('dashboard') }}" class="{{ $isActive('dashboard') }}">
                            {{ __('navigation.home') }}
                        </a>
                    </li>

                    @module('Puzzles')
                    @if(\App\Models\Character::getActiveCharacter()->isAn('developer'))
                    <li x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-1 {{ $isActive('module.puzzles.*') }}">
                            {{ __('navigation.puzzles') }}
                            <x-heroicon-o-chevron-down class="h-4 w-4"/>
                        </button>

                        <div
                            x-show="open"
                            @click.outside="open = false"
                            x-transition
                            class="absolute left-1/2 mt-2 w-44 -translate-x-1/2 rounded-md border border-navy-600 bg-navy-700 shadow-lg md:left-0 md:translate-x-0"
                        >
                            <a href="{{ route('modules.puzzles.albums') }}"
                               class="block rounded-t-md px-4 py-2 text-sm hover:bg-navy-600 {{ $isActive('modules.puzzles.albums') }}">
                                Albums
                            </a>
                            <a href="{{ route('modules.puzzles.puzzles') }}"
                               class="block px-4 py-2 text-sm hover:bg-navy-600 {{ $isActive('modules.puzzles.puzzles') }}">
                                Puzzles
                            </a>
                            <a href="{{ route('modules.puzzles.pieces') }}"
                               class="block rounded-b-md px-4 py-2 text-sm hover:bg-navy-600 {{ $isActive('modules.puzzles.pieces') }}">
                                Pieces
                            </a>
                        </div>
                    </li>
                    @endif
                    @endmodule

                    @if(\App\Models\Character::getActiveCharacter()->canAny(\App\Helpers\Permissions::USERS_SHOW, \App\Helpers\Permissions::MEDIA_GALLERY_VIEW))
                        <li x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-1 {{ $isActive('admin.*') }}">
                                {{ __('navigation.administration') }}
                                <x-heroicon-o-chevron-down class="h-4 w-4"/>
                            </button>

                            <div
                                x-show="open"
                                @click.outside="open = false"
                                x-transition
                                class="absolute left-1/2 mt-2 w-44 -translate-x-1/2 rounded-md border border-navy-600 bg-navy-700 shadow-lg md:left-0 md:translate-x-0"
                            >
                                @if(\App\Models\Character::getActiveCharacter()->can(\App\Helpers\Permissions::USERS_SHOW))
                                    <a href="{{ route('admin.users.list') }}"
                                       class="block rounded-t-md px-4 py-2 text-sm hover:bg-navy-600 {{ $isActive('admin.users.*') }}">
                                        Users
                                    </a>
                                @endif

                                @if(\App\Models\Character::getActiveCharacter()->can(App\Helpers\Permissions::MEDIA_GALLERY_VIEW))
                                    <a href="{{ route('admin.media.gallery') }}"
                                       class="block rounded-b-md px-4 py-2 text-sm hover:bg-navy-600 {{ $isActive('admin.media.*') }}">
                                        Media Gallery
                                    </a>
                                @endif
                            </div>
                        </li>
                    @endif
                </ul>

                {{-- Right: Language + User --}}
                <div class="flex items-center gap-2 order-2 md:order-3">
                    <livewire:database-notifications></livewire:database-notifications>

                    <x-language-switcher/>

                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-1 rounded-md p-1 hover:bg-navy-600">
                            <x-heroicon-o-user-circle class="h-6 w-6"/>
                            <span class="text-sm font-medium">{{ auth()->user()->username }}</span>
                        </button>

                        <div
                            x-show="open"
                            @click.outside="open = false"
                            x-transition
                            class="absolute right-0 mt-2 w-44 rounded-md border border-navy-600 bg-navy-700 shadow-lg"
                        >
                            <div class="border-b border-navy-600 px-4 py-2 text-xs text-slate-400">
                                Signed in as<br>
                                <span class="font-medium text-slate-200">{{ auth()->user()->username }}</span>
                            </div>

                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-slate-200 hover:bg-navy-600">
                                {{ __('navigation.profile') }}
                            </a>

                            <form method="GET" action="{{ route('auth.logout') }}">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 text-left text-sm text-red-400 hover:bg-navy-600">
                                    {{ __('navigation.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Content --}}
        <main class="pt-28 md:pt-20 px-6 min-h-[calc(100vh-(var(--spacing)*10))]">
            @include('flash-messages')
            {{ $slot }}
        </main>

        <div class="py-2">
            <p class="text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} KleinDev. All rights reserved.
            </p>
        </div>

        {{-- Notifications / Scripts --}}
{{--        @livewireScripts--}}
        @vite(['resources/js/app.js'])
        @livewireScriptConfig
        @filamentScripts
        @livewire('notifications')
        @vite(['resources/js/app.js'])

        {{-- Clipboard helper --}}
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('copy-to-clipboard', ({text, element}) => {
                    navigator.clipboard.writeText(text);

                    if (element !== undefined) {
                        const button = document.getElementById(element);
                        const tooltip = document.createElement('div');
                        tooltip.innerText = 'Copied!';

                        //Place above the element
                        const rect = button.getBoundingClientRect();
                        tooltip.style.left = `${rect.left + window.scrollX + rect.width / 2}px`;
                        tooltip.style.top = `${rect.top + window.scrollY - 30}px`;
                        tooltip.style.transform = 'translateX(-50%)';

                        // Basis-Klassen mit Transition
                        tooltip.className = 'absolute z-50 px-2 py-1 text-xs text-white bg-navy-800 rounded transition-opacity duration-300 opacity-0';
                        document.body.appendChild(tooltip);

                        // FadeIn: Nach dem Hinzufügen opacity auf 100 setzen
                        setTimeout(() => {
                            tooltip.classList.remove('opacity-0');
                            tooltip.classList.add('opacity-100');
                        }, 10);

                        // FadeOut: Nach 700ms opacity wieder auf 0, dann entfernen
                        setTimeout(() => {
                            tooltip.classList.remove('opacity-100');
                            tooltip.classList.add('opacity-0');

                            // Element erst nach der Fade-Out-Animation entfernen
                            setTimeout(() => {
                                document.body.removeChild(tooltip);
                            }, 300); // Wartet auf die 300ms transition-duration
                        }, 500);
                    }
                });
            });
        </script>

    </body>
</html>
