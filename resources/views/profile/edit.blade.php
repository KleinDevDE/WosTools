<x-layouts.mainlayout>
    <div class="max-w-2xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">{{ __('profile.title') }}</h1>
            <p class="text-navy-400">{{ __('profile.description') }}</p>
        </div>

        {{-- Profile Form --}}
        <form id="profileForm" method="POST" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf

            {{-- Display Name --}}
            <div class="bg-navy-800 rounded-xl border border-navy-700 p-6 grid grid-cols-2 gap-4">
                <h2 class="text-xl font-semibold text-white mb-4 col-span-2">{{ __('profile.username_section') }}</h2>

                <div>
                    <label for="display_name" class="block mb-2 text-sm font-medium text-navy-100">
                        {{ __('profile.display_name') }}
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
                            placeholder="{{ __('profile.display_name_placeholder') }}"
                        />
                    </div>
                    <p class="mt-2 text-xs text-navy-400">{{ __('profile.display_name_help') }}</p>
                    @error('display_name')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="username" class="block mb-2 text-sm font-medium text-navy-100">
                        {{ __('profile.username') }}
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
                            placeholder="{{ __('profile.username_placeholder') }}"
                            required
                        />
                    </div>
                    <p class="mt-2 text-xs text-navy-400">{{ __('profile.username_help') }}</p>
                    @error('username')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Password Change --}}
            <div class="bg-navy-800 rounded-xl border border-navy-700 p-6">
                <h2 class="text-xl font-semibold text-white mb-4">{{ __('profile.password_section') }}</h2>

                {{-- Current Password --}}
                <div class="mb-4">
                    <label for="current_password" class="block mb-2 text-sm font-medium text-navy-100">
                        {{ __('profile.current_password') }}
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
                        {{ __('profile.new_password') }}
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
                    <p class="mt-2 text-xs text-navy-400">{{ __('profile.password_help') }}</p>
                    @error('new_password')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex gap-4">
                <button
                    type="submit"
                    class="flex-1 rounded-xl bg-glow-500 hover:bg-glow-400 text-navy-900 font-semibold py-3 transition shadow-lg shadow-glow-500/20 focus:outline-none focus:ring-2 focus:ring-glow-400"
                >
                    {{ __('common.save') }}
                </button>
                <a
                    href="{{ route('dashboard') }}"
                    class="flex-1 rounded-xl bg-navy-700 hover:bg-navy-600 text-navy-100 font-semibold py-3 text-center transition border border-navy-600 focus:outline-none focus:ring-2 focus:ring-navy-500"
                >
                    {{ __('common.cancel') }}
                </a>
            </div>
        </form>
    </div>

    {{-- SHA256 Hashing Script --}}
    <script>
        document.getElementById('profileForm').addEventListener('submit', async function (event) {
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
