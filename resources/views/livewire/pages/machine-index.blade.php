<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <flux:heading>Machines</flux:heading>
        <flux:text class="mt-2">Monitor and manage laboratory machines.</flux:text>
    </div>

    <div class="mb-6 space-y-4">
        <div class="flex gap-4 items-end">
            <div class="flex-1 max-w-md">
                <flux:input
                    wire:model.live="filter"
                    label="Search machines"
                    placeholder="Filter..."
                    icon="magnifying-glass"
                />
            </div>
            <div>
                <flux:checkbox wire:model.live="includeMeta" label="Search metadata?" />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <flux:fieldset>
                <flux:legend>Status Filter</flux:legend>
                <flux:radio.group wire:model.live="statusFilter">
                    <flux:radio value="">All</flux:radio>
                    <flux:radio value="logged_in">Logged In</flux:radio>
                    <flux:radio value="not_logged_in">Not Logged In</flux:radio>
                    <flux:radio value="locked">Locked</flux:radio>
                    <flux:radio value="not_locked">Not Locked</flux:radio>
                </flux:radio.group>
            </flux:fieldset>

            <flux:fieldset>
                <flux:legend>OS Filter</flux:legend>
                <flux:radio.group wire:model.live="osFilter">
                    <flux:radio value="">Any OS</flux:radio>
                    <flux:radio value="Power">Windows</flux:radio>
                    <flux:radio value="curl">Linux</flux:radio>
                </flux:radio.group>
            </flux:fieldset>
        </div>
    </div>

    {{-- Machine Cards --}}
    <div x-cloak x-data="{ modalMachine: null }" @updatemachine="modalMachine = $event.detail.machine">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            @foreach ($machines as $machine)
            <flux:card class="relative {{ $machine->logged_in ? 'border-green-500' : '' }}">
                <div class="flex items-start gap-3" x-data="{ machine: {{ json_encode($machine) }} }">
                    <button @click="$dispatch('updatemachine', {machine: machine})" class="cursor-pointer">
                        <flux:icon.computer-desktop
                            class="w-12 h-12 {{ $machine->logged_in ? 'text-green-500 hover:text-green-600' : 'text-zinc-300 hover:text-zinc-400' }}"
                        />
                    </button>

                    <div class="flex-1 min-w-0">
                        <flux:heading size="sm" class="mb-1">{{ $machine->ip }}</flux:heading>

                        <flux:text variant="subtle" class="mb-2">{{ $machine->name ?? 'No name' }}</flux:text>

                        <flux:text variant="subtle" size="sm">
                            {{ $machine->updated_at->format('d/m/Y H:i') }}
                            <span class="text-xs">({{ $machine->updated_at->diffForHumans() }})</span>
                        </flux:text>
                    </div>

                    <div class="flex flex-col items-end gap-2">
                        <flux:button
                            wire:click="toggleLocked({{ $machine->id }})"
                            variant="{{ $machine->is_locked ? 'primary' : 'subtle' }}"
                            size="sm"
                            icon="{{ $machine->is_locked ? 'lock-closed' : 'lock-open' }}"
                            tooltip="{{ $machine->is_locked ? 'Locked' : 'Unlocked' }}"
                            inset="top bottom"
                        />

                        <div class="text-zinc-400">
                            @if (str_starts_with($machine->user_agent, 'curl'))
                                <span>
                                    <svg class="w-6 h-6" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" preserveAspectRatio="xMidYMid meet"><path d="M53.615 27.025c-2.098-1.303-3.775-2.14-5.063-2.59A42.449 42.449 0 0 1 48 17.467C48 8.926 40.836 2 32 2S16 8.926 16 17.467c0 2.695-.221 4.962-.553 6.969c-1.286.449-2.964 1.287-5.063 2.59C-.38 33.705 1.73 43.048 3.25 41.941c4.057-2.953 7.366-5.846 9.869-8.42C12.488 35.765 12 37.985 12 40.667c0 6.745 3.578 12.677 8.995 16.135c.197 1.171.449 3.271-.15 3.841c-.576.545-2.362-.115-3.176-.115C15.642 60.527 14 62 14 62h16.002s-1.643-1.473-3.668-1.473c-.814 0-2.602.66-3.176.115c-.447-.422-.422-1.688-.299-2.793C25.602 59.215 28.703 60 32 60s6.398-.784 9.139-2.149c.123 1.104.148 2.372-.297 2.794c-.574.545-2.361-.115-3.176-.115C35.643 60.529 34 62 34 62h16s-1.643-1.471-3.668-1.471c-.813 0-2.6.66-3.176.115c-.6-.569-.348-2.671-.15-3.842C48.422 53.344 52 47.413 52 40.667c0-2.682-.486-4.902-1.119-7.146c2.504 2.574 5.813 5.467 9.869 8.42c1.52 1.107 3.631-8.236-7.135-14.916M32 23.035l-4.488-1.095c.172-2.697 2.119-4.831 4.488-4.831c2.367 0 4.314 2.134 4.486 4.831L32 23.035m3.328.218c-.453 1.65-1.766 2.856-3.328 2.856c-1.566 0-2.877-1.206-3.33-2.857l3.33.812l3.328-.811M32 58.066c-8.283 0-16-5.578-16-17.144c0-6.423 4-12.466 4-22.134c0-11.682 8.957-10.208 10.008-2.251c-2.051.905-3.508 3.157-3.508 5.792v.393l1.109.271c.41 2.337 2.201 4.116 4.391 4.116c2.188 0 3.979-1.779 4.387-4.115l1.113-.271v-.393c0-2.635-1.459-4.887-3.508-5.792C35.041 8.58 44 7.108 44 18.789c0 9.668 4 15.711 4 22.134c0 11.565-7.715 17.143-16 17.143" fill="currentColor"></path><ellipse cx="39" cy="16.969" rx="2" ry="3" fill="#000000"></ellipse><path d="M24.998 13.969c-1.102 0-1.998 1.344-1.998 2.998c0 1.658.896 3.002 1.998 3.002c1.107 0 2.002-1.344 2.002-3.002c0-1.654-.895-2.998-2.002-2.998" fill="#000000"></path><path d="M32.973 17.543c-.145.166-.031.563.254.889c.281.325.629.455.773.289c.145-.164.031-.563-.252-.888s-.631-.456-.775-.29" fill="#000000"></path><path d="M30.25 17.833c-.285.325-.396.724-.254.888c.146.166.492.036.777-.289c.283-.325.396-.723.254-.889c-.144-.166-.494-.035-.777.29" fill="currentColor"></path></svg>
                                </span>
                                <flux:text variant="subtle" size="xs" class="mt-1">Linux</flux:text>
                            @else
                                <span>
                                    <svg fill="currentColor" class="w-6 h-6" viewBox="0 0 256 256" id="Flat" xmlns="http://www.w3.org/2000/svg">
  <path d="M112,144v51.63672a7.9983,7.9983,0,0,1-9.43115,7.87061l-64-11.63623A8.00019,8.00019,0,0,1,32,184V144a8.00008,8.00008,0,0,1,8-8h64A8.00008,8.00008,0,0,1,112,144ZM109.126,54.2217a7.995,7.995,0,0,0-6.55713-1.729l-64,11.63623A8.00017,8.00017,0,0,0,32,72v40a8.00008,8.00008,0,0,0,8,8h64a8.00008,8.00008,0,0,0,8-8V60.3633A7.99853,7.99853,0,0,0,109.126,54.2217Zm112-20.36377a7.99714,7.99714,0,0,0-6.55713-1.729l-80,14.5459A7.99965,7.99965,0,0,0,128,54.54543V112a8.00008,8.00008,0,0,0,8,8h80a8.00008,8.00008,0,0,0,8-8V40A8.00028,8.00028,0,0,0,221.126,33.85793ZM216,136H136a8.00008,8.00008,0,0,0-8,8v57.45459a7.99967,7.99967,0,0,0,6.56885,7.87061l80,14.5459A8.0001,8.0001,0,0,0,224,216V144A8.00008,8.00008,0,0,0,216,136Z"/>
                                </span>
                                <flux:text variant="subtle" size="xs" class="mt-1">Windows</flux:text>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($machine->logged_in)
                    <flux:badge variant="solid" color="green" class="absolute top-2 left-2" size="sm">Online</flux:badge>
                @endif
            </flux:card>
            @endforeach
        </div>

        <flux:modal name="machine-detail" x-show="modalMachine">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Machine Details</flux:heading>
                    <flux:text variant="subtle" x-text="modalMachine ? modalMachine.ip : ''"></flux:text>
                </div>

                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 overflow-auto">
                    <pre class="text-sm" x-html="modalMachine ? JSON.stringify(modalMachine, null, 2) : ''"></pre>
                </div>

                <div class="flex justify-end">
                    <flux:button @click="modalMachine = null">Close</flux:button>
                </div>
            </div>
        </flux:modal>

        <div class="mt-6">
            <flux:card variant="filled">
                <flux:text class="font-semibold">Total: {{ count($machines) }} machines</flux:text>
            </flux:card>
        </div>
    </div>
</div>
