<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Disable Inspect Element and Developer Tools -->
        <script>
        (function() {
            // Disable right-click context menu
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                return false;
            });

            // Disable keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // F12 key
                if (e.keyCode === 123) {
                    e.preventDefault();
                    return false;
                }
                
                // Ctrl+Shift+I (Windows/Linux) or Cmd+Option+I (Mac)
                if ((e.ctrlKey && e.shiftKey && e.keyCode === 73) || 
                    (e.metaKey && e.altKey && e.keyCode === 73)) {
                    e.preventDefault();
                    return false;
                }
                
                // Ctrl+Shift+C (Windows/Linux) or Cmd+Option+C (Mac)
                if ((e.ctrlKey && e.shiftKey && e.keyCode === 67) || 
                    (e.metaKey && e.altKey && e.keyCode === 67)) {
                    e.preventDefault();
                    return false;
                }
                
                // Ctrl+U (View Source)
                if (e.ctrlKey && e.keyCode === 85) {
                    e.preventDefault();
                    return false;
                }
                
                // Ctrl+Shift+J (Console)
                if (e.ctrlKey && e.shiftKey && e.keyCode === 74) {
                    e.preventDefault();
                    return false;
                }
            });

        })();
        </script>
    </head>
    <body class="font-sans antialiased">
        @yield('content')
    </body>
</html>
