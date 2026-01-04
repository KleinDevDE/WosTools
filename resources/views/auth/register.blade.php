<x-auth.layout>
    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-800">
        <div class="flex items-center gap-4">
            @if($playerProfile ?? false)
                <img
                    src="{{ $playerProfile->player_avatar_url }}"
                    alt="Player Avatar"
                    class="w-16 h-16 rounded-full border-2 border-primary-500">
            @else
                <div class="w-16 h-16 rounded-full border-2 border-gray-400 dark:border-gray-600 bg-gray-300 dark:bg-gray-700 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-500 dark:text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                    </svg>
                </div>
            @endif
            <div class="flex-1">
                <div class="flex justify-start items-center">
                    @if($playerProfile ?? false)
                        <img
                            src="{{ $playerProfile->furnace_level_icon }}"
                            alt="Furnace Level"
                            class="w-8 h-8">
                    @endif
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $userInvitation?->user?->player_name ?? '???' }}</h3>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $userInvitation?->user?->player_id ?? '???' }}</p>
                @if($playerProfile ?? false)
                    <p class="text-sm text-gray-500 dark:text-gray-400">State: {{ $playerProfile->state }}</p>
                @endif
            </div>
        </div>
    </div>
    <form
        id="registerForm"
        method="POST"
        action="{{ route('auth.register') }}"
        class="space-y-6 pt-4"
    >
        @csrf
        {{-- Display Name --}}
        <div>
            <label
                for="display_name"
                class="block mb-2 text-sm font-medium text-navy-100"
            >
                Display Name
                <small>
                    (Optional)
                </small>
            </label>

            <div
                class="flex rounded-xl overflow-hidden border border-white/10 bg-navy-800 focus-within:ring-2 focus-within:ring-glow-400/60 transition">
            <span class="inline-flex items-center px-3 text-navy-300 bg-navy-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
            </span>

                <input
                    type="text"
                    id="display_name"
                    name="display_name"
                    autocomplete="name"
                    class="w-full bg-transparent px-4 py-3 text-navy-50 placeholder-navy-400 focus:outline-none border-0"
                    placeholder="Choose your display name"
                    value="{{ old('display_name', $userInvitation?->user?->display_name ?? '') }}"
                    autofocus
                />
            </div>

            @error('display_name')
            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label
                for="password"
                class="block mb-2 text-sm font-medium text-navy-100"
            >
                Password
            </label>

            <div
                class="flex rounded-xl overflow-hidden border border-white/10 bg-navy-800 focus-within:ring-2 focus-within:ring-glow-400/60 transition">
            <span class="inline-flex items-center px-3 text-navy-300 bg-navy-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M16.5 10.5V6.75
                             a4.5 4.5 0 1 0-9 0v3.75
                             m-.75 11.25h10.5
                             a2.25 2.25 0 0 0 2.25-2.25
                             v-6.75
                             a2.25 2.25 0 0 0-2.25-2.25H6.75
                             a2.25 2.25 0 0 0-2.25 2.25v6.75
                             a2.25 2.25 0 0 0 2.25 2.25Z"/>
                </svg>
            </span>

                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="new-password"
                    class="w-full bg-transparent px-4 py-3 text-navy-50 placeholder-navy-400 focus:outline-none border-0"
                    placeholder="••••••••"
                    required
                />
            </div>

            @error('password')
            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="w-full rounded-xl bg-glow-500 hover:bg-glow-400
               text-navy-900 font-semibold py-3
               transition shadow-lg shadow-glow-500/20
               focus:outline-none focus:ring-2 focus:ring-glow-400"
        >
            Register
        </button>
    </form>


    <script>
        //Password in form must be hashed before sending
        //The password still gets hashed with Argon2id on the server side
        //It's only to prevent observers getting the plain password
        document.getElementById('registerForm').addEventListener('submit', async function (event) {
            event.preventDefault();
            const form = event.target;
            const passwordField = form.querySelector('input[name="password"]');
            const password = passwordField.value;

            // Hash the password using SHA-256
            // passwordField.value = sha256(password);

            // Create a hidden input to carry the hashed password so the visible field is not altered
            let hidden = form.querySelector('#password_hashed');
            if (!hidden) {
                hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.style.display = 'none';
                hidden.id = 'password_hashed';
                hidden.name = 'password';
                form.appendChild(hidden);
            }

            // Compute hash (supports sync or promise-based sha256)
            const hashed = await sha256(password);
            hidden.value = hashed;

            // Prevent the plain password from being submitted or briefly shown
            passwordField.value = '';
            passwordField.removeAttribute('name');

            form.submit();
            return false;
        });
    </script>
</x-auth.layout>
