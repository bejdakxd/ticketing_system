<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @livewireStyles

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white">
        <x-flash-messages></x-flash-messages>
        <div class="min-h-screen">
            <x-navbar />
            <!-- Page Content -->
            <main>
                <div class="flex flex-row">
                    @auth
                        @if(auth()->user()->hasPermissionTo('view_resolver_panel'))
                            <livewire:resolver-panel />
                        @endif
                    @endauth
                    <div class="w-full px-8 py-4">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
        @livewireScripts
    </body>
</html>
