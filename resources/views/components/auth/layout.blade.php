<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Login' }} | WoSTools</title>
        @vite(['resources/css/app.css'])
        @livewireStyles
    </head>

    <body class="min-h-screen flex items-center justify-center bg-navy-950">
        {{-- Subtle grid pattern background --}}
        <div class="absolute inset-0 bg-[linear-gradient(rgba(100,200,255,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(100,200,255,0.03)_1px,transparent_1px)] bg-[size:64px_64px]"></div>

        {{-- Main auth card --}}
        <div class="relative w-full max-w-md mx-4">
            <div class="bg-navy-900 rounded-2xl border border-navy-700 shadow-2xl shadow-glow-500/10 overflow-hidden">
                {{-- Header --}}
                <div class="px-8 pt-8 pb-6 border-b border-navy-700">
                    <div class="flex items-center justify-center gap-3 mb-4">
                        <x-heroicon-o-sparkles class="w-8 h-8 text-glow-400"/>
                        <h1 class="text-2xl font-bold text-white">WoSTools</h1>
                    </div>
                    <p class="text-center text-navy-400 text-sm">
                        Whiteout Survival Community Tools
                    </p>
                </div>

                {{-- Content slot --}}
                <div class="p-8">
                    {{ $slot }}
                </div>
            </div>

            {{-- Footer text --}}
            <p class="text-center text-navy-500 text-sm mt-6">
                &copy; {{ date('Y') }} WoSTools. Community Project.
            </p>
        </div>

        @vite(['resources/js/app.js'])
    </body>
</html>
