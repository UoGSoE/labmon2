<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body>
    <div id="app">
        @if (Auth::check())
        @include('layouts.navbar')
        <main class="container mx-auto px-4">
            @yield('content')
        </main>
        @else
            @yield('content')
        @endif
        <div class="h-16 mt-8 mb-8"></div>
    </div>
    @livewireScripts
    @stack('scripts')
</body>

</html>
