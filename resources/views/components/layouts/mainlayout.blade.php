<html>
    <head>
        <title>WoSTools</title>
        @vite(['resources/css/app.css'])
        @livewireStyles
        @filamentStyles
    </head>

    <body class="min-h-screen center bg-gray-100">
{{--        <livewire:toasts />--}}
        {{ $slot }}
        @vite(['resources/js/app.js'])
        @livewireScriptConfig
        @filamentScripts
        @livewire('notifications')
        <script>
            document.addEventListener('livewire:init', () => {
                console.log("Init")
                Livewire.on('copy-to-clipboard', ({ text, element }) => {
                    console.log("A")
                    copyToClipboard(text)

                    if (element !== undefined) {
                        const button = document.getElementById(element);
                        const tooltip = document.createElement('div');
                        const rect = button.getBoundingClientRect();
                        tooltip.style.top = `${rect.top - 30}px`;
                        tooltip.style.left = `${rect.left + rect.width / 2 - 30}px`;
                        tooltip.innerText = 'Copied!';

                        // Basis-Klassen mit Transition
                        tooltip.className = 'absolute z-50 px-2 py-1 text-xs text-white bg-black rounded transition-opacity duration-300 opacity-0';
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
                    } else {
                        Toast.success('Copied to clipboard');
                    }
                });
            });
        </script>
    </body>
</html>
