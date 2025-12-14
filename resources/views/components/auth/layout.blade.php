<html>
    <head>
        <title>Login | WoSTools</title>
        @vite(['resources/css/app.css'])
        @livewireStyles
    </head>

    <body class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="w-full max-w-md bg-white p-8 rounded shadow">
            <h2 class="text-2xl font-bold mb-6 text-center">Login to WoSTools</h2>
            {{$slot}}
        </div>

        @vite(['resources/js/app.js'])
    </body>
</html>
