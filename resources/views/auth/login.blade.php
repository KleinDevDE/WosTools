<x-auth.layout>
    <form id="loginForm" method="POST" action="{{ route('auth.login') }}">
        @csrf
        <input type="text" name="username" placeholder="username" value="{{ old('username') }}">
        @error('username')
        <p>{{ $message }}</p>
        @enderror
        <input type="password" name="password" placeholder="Password">
        @error('password')
        <p>{{ $message }}</p>
        @enderror
        <button type="submit">Login</button>
    </form>

    <script>
        //Password in form must be hashed before sending
        //The password still gets hashed with Argon2id on the server side
        //It's only to prevent observers getting the plain password
        document.getElementById('loginForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            const form = event.target;
            const passwordField = form.querySelector('input[name="password"]');
            const password = passwordField.value;

            // Hash the password using SHA-256
            passwordField.value = sha256(password);

            form.submit();
            return false;
        });
    </script>
</x-auth.layout>
