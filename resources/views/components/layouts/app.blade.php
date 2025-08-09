<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @fluxAppearance
    @livewireStyles
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:header class="bg-white border-b border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
            <div class="flex flex-col md:flex-row justify-between items-center py-4 w-full">
              <div class="flex gap-6">
                <flux:heading size="lg">{{ config('app.name', 'LabMon') }}</flux:heading>

                @auth
                    <nav class="hidden md:flex space-x-8">
                        <a href="{{ route('home') }}" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100">Labs</a>
                        <a href="{{ route('machine.index') }}" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100">Machines</a>
                        <a href="{{ route('options.edit') }}" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100">Options</a>
                    </nav>
                @endauth
              </div>

              @auth
                  <div>
                    <flux:button size="sm" inset>Log Out</flux:button>
                  </div>
              @endauth
            </div>
    </flux:header>

    <flux:main>
        {{ $slot }}
    </flux:main>

    @fluxScripts
    @livewireScripts
    @stack('scripts')
</body>

</html>
