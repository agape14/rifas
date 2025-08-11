<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Plataforma de Rifas') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen grid grid-cols-1 md:grid-cols-2 bg-gray-100 dark:bg-gray-900">
            <!-- Branding / Hero -->
            <div class="hidden md:flex flex-col items-center justify-center p-10 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 text-white">
                <div class="max-w-md text-center space-y-4">
                    <div class="text-6xl">🎟️</div>
                    <h1 class="text-3xl font-bold tracking-tight">{{ config('app.name', 'Plataforma de Rifas') }}</h1>
                    <p class="text-white/90">Gestiona tus sorteos, vende números y realiza el sorteo en vivo con transparencia.</p>
                </div>
            </div>

            <!-- Auth Card -->
            <div class="flex flex-col sm:justify-center items-center py-10 md:py-0 px-4 sm:px-6">
                <!-- Mobile branding -->
                <div class="md:hidden mb-6 text-center">
                    <div class="text-5xl">🎟️</div>
                    <div class="mt-2 text-lg font-semibold text-gray-800 dark:text-gray-200">{{ config('app.name', 'Plataforma de Rifas') }}</div>
                </div>

                <div class="w-full sm:max-w-md px-6 py-6 sm:py-8 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
