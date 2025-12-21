<x-layouts.mainlayout>
    <div class="min-h-screen bg-navy-950">
        {{-- Hero Section --}}
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-white mb-4">
                    Welcome, {{ auth()->user()->username }}! 👋
                </h1>
                <p class="text-lg text-navy-400">
                    Choose a module to get started
                </p>
            </div>

            {{-- Navigation Cards Grid --}}
            <div class="grid grid-cols-2 gap-6 max-w-5xl mx-auto">

                {{-- Puzzles Module Card --}}
                <a href="{{ route('modules.puzzles.spa') }}"
                   class="group bg-navy-900 rounded-2xl border border-navy-700 p-8
                          hover:border-glow-500 hover:shadow-lg hover:shadow-glow-500/20
                          transition-all duration-300 cursor-pointer transform hover:-translate-y-1">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 mb-4 text-glow-400 group-hover:text-glow-300 transition-colors">
                            <x-heroicon-o-puzzle-piece class="w-full h-full"/>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Puzzle Trading</h3>
                        <p class="text-navy-400 text-sm">
                            Manage your puzzle pieces, find trading partners, and complete your collection
                        </p>
                    </div>
                </a>

                {{-- User Profile Card --}}
{{--                <a href="{{ route('user.profile') }}"--}}
{{--                   class="group bg-navy-900 rounded-2xl border border-navy-700 p-8--}}
{{--                          hover:border-glow-500 hover:shadow-lg hover:shadow-glow-500/20--}}
{{--                          transition-all duration-300 cursor-pointer transform hover:-translate-y-1">--}}
{{--                    <div class="flex flex-col items-center text-center">--}}
{{--                        <div class="w-16 h-16 mb-4 text-glow-400 group-hover:text-glow-300 transition-colors">--}}
{{--                            <x-heroicon-o-user-circle class="w-full h-full"/>--}}
{{--                        </div>--}}
{{--                        <h3 class="text-xl font-bold text-white mb-2">Profile & Settings</h3>--}}
{{--                        <p class="text-navy-400 text-sm">--}}
{{--                            Manage your account, change password, and update your preferences--}}
{{--                        </p>--}}
{{--                    </div>--}}
{{--                </a>--}}

                {{-- Administration Card (Admin Only) --}}
                @hasrole('developer')
                <a href="{{ route('admin.users.list') }}"
                   class="group bg-navy-900 rounded-2xl border border-navy-700 p-8
                          hover:border-glow-500 hover:shadow-lg hover:shadow-glow-500/20
                          transition-all duration-300 cursor-pointer transform hover:-translate-y-1">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 mb-4 text-glow-400 group-hover:text-glow-300 transition-colors">
                            <x-heroicon-o-cog-6-tooth class="w-full h-full"/>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Administration</h3>
                        <p class="text-navy-400 text-sm">
                            Manage users, access requests, and system settings
                        </p>
                        <span class="mt-2 px-2 py-1 bg-glow-600/20 border border-glow-500 rounded-full text-glow-400 text-xs font-bold">
                            Admin Only
                        </span>
                    </div>
                </a>
                @endif

            </div>

            {{-- Quick Stats (Optional) --}}
            <div class="mt-16 max-w-5xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-navy-900/50 rounded-xl border border-navy-700 p-4 text-center">
                        <div class="text-2xl font-bold text-glow-400">
                            {{ auth()->user()->created_at->diffForHumans() }}
                        </div>
                        <div class="text-sm text-navy-400 mt-1">Member Since</div>
                    </div>

                    <div class="bg-navy-900/50 rounded-xl border border-navy-700 p-4 text-center">
                        <div class="text-2xl font-bold text-success-400">
                            Active
                        </div>
                        <div class="text-sm text-navy-400 mt-1">Account Status</div>
                    </div>

                    <div class="bg-navy-900/50 rounded-xl border border-navy-700 p-4 text-center">
                        <div class="text-2xl font-bold text-white">
                            {{ auth()->user()->username }}
                        </div>
                        <div class="text-sm text-navy-400 mt-1">Username</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.mainlayout>
