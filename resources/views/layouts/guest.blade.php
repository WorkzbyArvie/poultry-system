<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1 text-white">
        <title>Poultry System Login</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            /* Force background visibility for laptop users */
            body { background-color: #1a1a1a !important; }
            .login-card { background-color: #ffffff !important; color: #1a1a1a !important; padding: 2rem; border-radius: 0.5rem; }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="login-card w-full sm:max-w-md shadow-md overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>