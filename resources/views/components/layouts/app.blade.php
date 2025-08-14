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
            <div class="flex flex-row justify-between items-center py-4 w-full">
                <flux:heading size="lg">
                    <a href="{{ route('home') }}" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100">
                        {{ config('app.name', 'LabMon') }}
                    </a>
                </flux:heading>

                @auth
                    <!-- Mobile burger menu -->
                    <div class="md:hidden">
                        <flux:dropdown position="bottom" align="start">
                            <flux:button variant="ghost" icon="bars-3" size="sm"></flux:button>

                            <flux:navmenu>
                                <flux:navmenu.item href="{{ route('home') }}" icon="building-office-2">Labs</flux:navmenu.item>
                                <flux:navmenu.item href="{{ route('machine.index') }}" icon="computer-desktop">Machines</flux:navmenu.item>
                                <flux:navmenu.item href="{{ route('options.edit') }}" icon="cog-6-tooth">Options</flux:navmenu.item>
                                <flux:navmenu.item href="{{ route('auth.logout') }}" icon="circle-stack">Log Out</flux:navmenu.item>
                            </flux:navmenu>
                        </flux:dropdown>
                    </div>

                    <nav class="hidden md:flex space-x-8">
                        <a href="{{ route('home') }}" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100">Labs</a>
                        <a href="{{ route('machine.index') }}" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100">Machines</a>
                        <a href="{{ route('options.edit') }}" class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100">Options</a>
                    </nav>

                    <div class="hidden md:block">
                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <flux:button size="xs" class="cursor-pointer" type="submit" inset>Log Out</flux:button>
                        </form>
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
