<x-layouts.mainlayout>
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Settings</h1>
            <p class="text-navy-400">Manage your account and characters</p>
        </div>

        {{-- Account Settings --}}
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-white mb-4">Account Settings</h2>

            <form id="accountForm" method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf

                <div class="bg-navy-800 rounded-xl border border-navy-700 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Username --}}
                        <div>
                            <label for="username" class="block mb-2 text-sm font-medium text-navy-100">
                                Username
                            </label>
                            <div class="flex rounded-xl overflow-hidden border border-white/10 bg-navy-900 focus-within:ring-2 focus-within:ring-glow-400/60 transition">
                                <span class="inline-flex items-center px-3 text-navy-300 bg-navy-700">
                                    <x-heroicon-o-user class="size-5"/>
                                </span>
                                <input
                                    type="text"
                                    id="username"
                                    name="username"
                                    value="{{ old('username', $user->username) }}"
                                    class="w-full bg-transparent px-4 py-3 text-navy-50 placeholder-navy-400 focus:outline-none border-0"
                                    required
                                />
                            </div>
                            @error('username')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Display Name --}}
                        <div>
                            <label for="display_name" class="block mb-2 text-sm font-medium text-navy-100">
                                Display Name <span class="text-navy-400">(optional)</span>
                            </label>
                            <div class="flex rounded-xl overflow-hidden border border-white/10 bg-navy-900 focus-within:ring-2 focus-within:ring-glow-400/60 transition">
                                <span class="inline-flex items-center px-3 text-navy-300 bg-navy-700">
                                    <x-heroicon-o-identification class="size-5"/>
                                </span>
                                <input
                                    type="text"
                                    id="display_name"
                                    name="display_name"
                                    value="{{ old('display_name', $user->display_name) }}"
                                    class="w-full bg-transparent px-4 py-3 text-navy-50 placeholder-navy-400 focus:outline-none border-0"
                                    placeholder="Optional display name"
                                />
                            </div>
                            @error('display_name')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-navy-700">
                        <button
                            type="submit"
                            class="rounded-xl bg-glow-500 hover:bg-glow-400 text-navy-900 font-semibold px-6 py-2.5 transition shadow-lg shadow-glow-500/20 focus:outline-none focus:ring-2 focus:ring-glow-400"
                        >
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>

            {{-- Password Change --}}
            <form id="passwordForm" method="POST" action="{{ route('profile.update') }}" class="mt-4">
                @csrf

                <div class="bg-navy-800 rounded-xl border border-navy-700 p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Change Password</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Current Password --}}
                        <div>
                            <label for="current_password" class="block mb-2 text-sm font-medium text-navy-100">
                                Current Password
                            </label>
                            <div class="flex rounded-xl overflow-hidden border border-white/10 bg-navy-900 focus-within:ring-2 focus-within:ring-glow-400/60 transition">
                                <span class="inline-flex items-center px-3 text-navy-300 bg-navy-700">
                                    <x-heroicon-o-lock-closed class="size-5"/>
                                </span>
                                <input
                                    type="password"
                                    id="current_password"
                                    name="current_password"
                                    autocomplete="current-password"
                                    class="w-full bg-transparent px-4 py-3 text-navy-50 placeholder-navy-400 focus:outline-none border-0"
                                    placeholder="••••••••"
                                />
                            </div>
                            @error('current_password')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- New Password --}}
                        <div>
                            <label for="new_password" class="block mb-2 text-sm font-medium text-navy-100">
                                New Password
                            </label>
                            <div class="flex rounded-xl overflow-hidden border border-white/10 bg-navy-900 focus-within:ring-2 focus-within:ring-glow-400/60 transition">
                                <span class="inline-flex items-center px-3 text-navy-300 bg-navy-700">
                                    <x-heroicon-o-lock-closed class="size-5"/>
                                </span>
                                <input
                                    type="password"
                                    id="new_password"
                                    name="new_password"
                                    autocomplete="new-password"
                                    class="w-full bg-transparent px-4 py-3 text-navy-50 placeholder-navy-400 focus:outline-none border-0"
                                    placeholder="••••••••"
                                />
                            </div>
                            @error('new_password')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-navy-700">
                        <button
                            type="submit"
                            class="rounded-xl bg-glow-500 hover:bg-glow-400 text-navy-900 font-semibold px-6 py-2.5 transition shadow-lg shadow-glow-500/20 focus:outline-none focus:ring-2 focus:ring-glow-400"
                        >
                            Update Password
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Characters --}}
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-white mb-4">Your Characters</h2>

            <div class="bg-navy-800 rounded-xl border border-navy-700 p-6">
                <div class="space-y-3">
                    @forelse($user->characters as $character)
                        <div class="flex items-center justify-between p-4 bg-navy-900 rounded-lg border border-navy-700">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-lg font-semibold text-white">{{ $character->player_name }}</h3>
                                    <span class="text-sm text-navy-400">#{{ $character->player_id }}</span>
                                </div>
                                <div class="flex items-center gap-4 text-sm text-navy-300">
                                    @if($character->stateRelation)
                                        <span>State {{ $character->state }}</span>
                                    @endif
                                    @if($character->alliance)
                                        <span>{{ $character->alliance->name }}</span>
                                    @endif
                                    @if($character->roles->isNotEmpty())
                                        <span class="text-sky-400">{{ $character->roles->pluck('name')->join(', ') }}</span>
                                    @endif
                                </div>
                            </div>

                            @if($user->characters->count() > 1)
                                <form method="POST" action="{{ route('characters.delete', $character->id) }}" class="ml-4">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        onclick="return confirm('Are you sure you want to delete {{ $character->player_name }}? This action cannot be undone.')"
                                        class="rounded-lg bg-red-900/20 hover:bg-red-900/40 text-red-400 px-4 py-2 text-sm font-medium transition border border-red-800/50"
                                    >
                                        Delete
                                    </button>
                                </form>
                            @else
                                <span class="ml-4 text-sm text-navy-400">Cannot delete last character</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-navy-400 text-center py-8">No characters found</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-red-400 mb-4">Danger Zone</h2>

            <div class="bg-red-900/10 rounded-xl border border-red-800/50 p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white mb-2">Delete Account</h3>
                        <p class="text-navy-300 text-sm mb-4">
                            Once you delete your account, there is no going back. This will permanently delete your account, all characters, and all associated data.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('profile.delete') }}" class="ml-4">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            onclick="return confirm('Are you absolutely sure? This will delete your account and ALL characters permanently. This action cannot be undone!')"
                            class="rounded-lg bg-red-600 hover:bg-red-500 text-white px-6 py-2.5 font-semibold transition shadow-lg shadow-red-600/20 focus:outline-none focus:ring-2 focus:ring-red-400"
                        >
                            Delete Account
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- SHA256 Hashing Script --}}
    <script>
        document.getElementById('passwordForm').addEventListener('submit', async function (event) {
            const form = event.target;
            const currentPasswordField = form.querySelector('#current_password');
            const newPasswordField = form.querySelector('#new_password');

            // Only hash if password fields are filled
            if (currentPasswordField.value || newPasswordField.value) {
                event.preventDefault();

                // Hash current password
                if (currentPasswordField.value) {
                    let hiddenCurrent = form.querySelector('#current_password_hashed');
                    if (!hiddenCurrent) {
                        hiddenCurrent = document.createElement('input');
                        hiddenCurrent.type = 'hidden';
                        hiddenCurrent.id = 'current_password_hashed';
                        hiddenCurrent.name = 'current_password';
                        form.appendChild(hiddenCurrent);
                    }
                    const hashedCurrent = await sha256(currentPasswordField.value);
                    hiddenCurrent.value = hashedCurrent;
                    currentPasswordField.value = '';
                    currentPasswordField.removeAttribute('name');
                }

                // Hash new password
                if (newPasswordField.value) {
                    let hiddenNew = form.querySelector('#new_password_hashed');
                    if (!hiddenNew) {
                        hiddenNew = document.createElement('input');
                        hiddenNew.type = 'hidden';
                        hiddenNew.id = 'new_password_hashed';
                        hiddenNew.name = 'new_password';
                        form.appendChild(hiddenNew);
                    }
                    const hashedNew = await sha256(newPasswordField.value);
                    hiddenNew.value = hashedNew;
                    newPasswordField.value = '';
                    newPasswordField.removeAttribute('name');
                }

                form.submit();
                return false;
            }
        });
    </script>
</x-layouts.mainlayout>
