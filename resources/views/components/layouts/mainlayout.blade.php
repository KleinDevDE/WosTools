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

    <body class="min-h-screen bg-slate-50 text-slate-600
             dark:bg-navy-900 dark:text-navy-200">

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
        <nav class="fixed top-0 z-50 w-full border-b border-navy-700 h-15.25 bg-white dark:bg-navy-700">
            <div class="px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-10">
                    {{-- Branding --}}
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-heroicon-o-sparkles class="w-6 h-6 text-sky-400"/>
                        <span class="text-lg font-semibold whitespace-nowrap">
                            Whiteout Survival
                        </span>
                    </a>

                    {{-- Navigation --}}
                    <ul class="flex items-center gap-6 text-sm font-medium">
                        <li>
                            <a href="{{ route('dashboard') }}"
                               class="{{ $isActive('dashboard') }}">
                                {{ __('navigation.home') }}
                            </a>
                        </li>

                        @module('Puzzles')
                        @role('developer')
                        <li x-data="{ open: false }" class="relative">
                            <button
                                @click="open = !open"
                                class="flex items-center gap-1 {{ $isActive('module.puzzles.*') }}"
                            >
                                {{ __('navigation.puzzles') }}
                                <x-heroicon-o-chevron-down class="w-4 h-4"/>
                            </button>

                            <div
                                x-show="open"
                                @click.outside="open = false"
                                x-transition
                                class="absolute left-0 mt-2 w-44 rounded-md
                               bg-navy-700 border border-navy-600 shadow-lg"
                            >
                                <a href="{{ route('modules.puzzles.albums') }}"
                                   class="block px-4 py-2 text-sm hover:bg-navy-600 rounded-t-md
                                  {{ $isActive('modules.puzzles.albums') }}">
                                    Albums
                                </a>
                                <a href="{{ route('modules.puzzles.puzzles') }}"
                                   class="block px-4 py-2 text-sm hover:bg-navy-600
                                  {{ $isActive('modules.puzzles.puzzles') }}">
                                    Puzzles
                                </a>
                                <a href="{{ route('modules.puzzles.pieces') }}"
                                   class="block px-4 py-2 text-sm hover:bg-navy-600  rounded-b-md
                                  {{ $isActive('modules.puzzles.pieces') }}">
                                    Pieces
                                </a>
                            </div>
                        </li>
                        @endrole
                        @endmodule

                        {{-- Administration Dropdown --}}
                        @canany([\App\Helpers\Permissions::USERS_SHOW, \App\Helpers\Permissions::MEDIA_GALLERY_VIEW])
                        <li x-data="{ open: false }" class="relative">
                            <button
                                @click="open = !open"
                                class="flex items-center gap-1 {{ $isActive('admin.*') }}"
                            >
                                {{ __('navigation.administration') }}
                                <x-heroicon-o-chevron-down class="w-4 h-4"/>
                            </button>

                            <div
                                x-show="open"
                                @click.outside="open = false"
                                x-transition
                                class="absolute left-0 mt-2 w-44 rounded-md
                               bg-navy-700 border border-navy-600 shadow-lg"
                            >
                                @can(App\Helpers\Permissions::USERS_SHOW)
                                <a href="{{ route('admin.users.list') }}"
                                   class="block px-4 py-2 text-sm hover:bg-navy-600 rounded-t-md
                                  {{ $isActive('admin.users.*') }}">
                                    Users
                                </a>
                                @endcan

                                @can(App\Helpers\Permissions::MEDIA_GALLERY_VIEW)
                                <a href="{{ route('admin.media.gallery') }}"
                                   class="block px-4 py-2 text-sm hover:bg-navy-600 rounded-b-md
                                  {{ $isActive('admin.media.*') }}">
                                    Media Gallery
                                </a>
                                @endcan
                            </div>
                        </li>
                        @endcanany
                    </ul>
                </div>

                {{-- Right: Language switcher + User menu --}}
                <div class="flex items-center gap-2">
                    {{-- Language Switcher --}}
                    <x-language-switcher/>

                    {{-- User Menu --}}
                    <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        class="flex items-center gap-2 px-3 py-2 rounded-md
                       hover:bg-navy-600"
                    >
                        <x-heroicon-o-user-circle class="w-6 h-6"/>
                        <span class="text-sm font-medium">
                    {{ auth()->user()->username }}
                </span>
                        <x-heroicon-o-chevron-down class="w-4 h-4"/>
                    </button>

                    <div
                        x-show="open"
                        @click.outside="open = false"
                        x-transition
                        class="absolute right-0 mt-2 w-44 rounded-md
                       bg-navy-700 border border-navy-600 shadow-lg"
                    >
                        <div class="px-4 py-2 text-xs text-slate-400
                            border-b border-navy-600">
                            Signed in as<br>
                            <span class="font-medium text-slate-200">
                        {{ auth()->user()->username }}
                    </span>
                        </div>

                        <form method="GET" action="{{ route('auth.logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm
                                   text-red-400 hover:bg-navy-600">
                                {{ __('navigation.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
                </div>

            </div>
        </nav>

        {{-- Content --}}
        <main class="mt-15.25 p-4">
            {{ $slot }}
        </main>

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
                Livewire.on('copy-to-clipboard', ({text}) => {
                    navigator.clipboard.writeText(text);
                });
            });
        </script>

    </body>
</html>
