<div>
    <div x-data="{showFilters: false}" class="mb-6 space-y-4">
        <div class="flex gap-4 items-end">
            <div class="flex-1 max-w-md">
                <flux:input
                    wire:model.live="filter"
                    placeholder="Search for..."
                    icon="magnifying-glass"
                />
            </div>
            <div>
                <flux:checkbox wire:model.live="includeMeta" label="Search metadata?" />
                <flux:checkbox x-on:click="showFilters = !showFilters" label="Advanced Filters" />
            </div>
        </div>

        <div x-show="showFilters" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <flux:fieldset>
                <flux:radio.group wire:model.live="statusFilter" variant="segmented">
                    <flux:radio value="" label="All" />
                    <flux:radio value="logged_in" label="Logged In" />
                    <flux:radio value="not_logged_in" label="Not Logged In" />
                    <flux:radio value="locked" label="Locked" />
                    <flux:radio value="not_locked" label="Not Locked" />
                </flux:radio.group>
            </flux:fieldset>

            <flux:fieldset>
                <flux:radio.group wire:model.live="osFilter" variant="segmented">
                    <flux:radio value="" label="Any OS" />
                    <flux:radio value="Power" label="Windows" />
                    <flux:radio value="curl" label="Linux" />
                </flux:radio.group>
            </flux:fieldset>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @foreach ($machines as $machine)
        <flux:card>
            <div class="flex items-start gap-3">
                <div class="flex-1 min-w-0 cursor-pointer" wire:click="openMachineModal({{ $machine->id }})">

                    <flux:heading size="sm" class="mb-1 flex items-center gap-2">
                        <flux:icon.computer-desktop
                            class="size-8 {{ $machine->logged_in ? 'text-green-500 hover:text-green-600' : 'text-zinc-300 hover:text-zinc-400' }}"
                        />

                        <span class="hover:text-zinc-200">{{ $machine->ip }}</span>
                    </flux:heading>

                    <flux:text variant="subtle" class="mb-2">{{ $machine->name ?? 'No name' }}</flux:text>

                    <flux:text variant="subtle" size="sm">
                        {{ $machine->updated_at->format('d/m/Y H:i') }}
                        <span class="text-xs">({{ $machine->updated_at->diffForHumans() }})</span>
                    </flux:text>
                </div>

                <flux:separator orientation="vertical" />

                <div class="flex flex-col items-center gap-2">
                    <flux:button
                        wire:click="toggleLocked({{ $machine->id }})"
                        variant="{{ $machine->is_locked ? 'primary' : 'subtle' }}"
                        size="sm"
                        icon="{{ $machine->is_locked ? 'lock-closed' : 'lock-open' }}"
                        tooltip="{{ $machine->is_locked ? 'Locked' : 'Unlocked' }}"
                        inset="top bottom"
                    />

                    <flux:separator />

                    <div class="text-zinc-400 flex flex-col items-center gap-2">
                        @if (str_starts_with($machine->user_agent, 'curl'))
                            <flux:tooltip content="Linux">
                                <svg class="w-6 h-6" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" preserveAspectRatio="xMidYMid meet"><path d="M53.615 27.025c-2.098-1.303-3.775-2.14-5.063-2.59A42.449 42.449 0 0 1 48 17.467C48 8.926 40.836 2 32 2S16 8.926 16 17.467c0 2.695-.221 4.962-.553 6.969c-1.286.449-2.964 1.287-5.063 2.59C-.38 33.705 1.73 43.048 3.25 41.941c4.057-2.953 7.366-5.846 9.869-8.42C12.488 35.765 12 37.985 12 40.667c0 6.745 3.578 12.677 8.995 16.135c.197 1.171.449 3.271-.15 3.841c-.576.545-2.362-.115-3.176-.115C15.642 60.527 14 62 14 62h16.002s-1.643-1.473-3.668-1.473c-.814 0-2.602.66-3.176.115c-.447-.422-.422-1.688-.299-2.793C25.602 59.215 28.703 60 32 60s6.398-.784 9.139-2.149c.123 1.104.148 2.372-.297 2.794c-.574.545-2.361-.115-3.176-.115C35.643 60.529 34 62 34 62h16s-1.643-1.471-3.668-1.471c-.813 0-2.6.66-3.176.115c-.6-.569-.348-2.671-.15-3.842C48.422 53.344 52 47.413 52 40.667c0-2.682-.486-4.902-1.119-7.146c2.504 2.574 5.813 5.467 9.869 8.42c1.52 1.107 3.631-8.236-7.135-14.916M32 23.035l-4.488-1.095c.172-2.697 2.119-4.831 4.488-4.831c2.367 0 4.314 2.134 4.486 4.831L32 23.035m3.328.218c-.453 1.65-1.766 2.856-3.328 2.856c-1.566 0-2.877-1.206-3.33-2.857l3.33.812l3.328-.811M32 58.066c-8.283 0-16-5.578-16-17.144c0-6.423 4-12.466 4-22.134c0-11.682 8.957-10.208 10.008-2.251c-2.051.905-3.508 3.157-3.508 5.792v.393l1.109.271c.41 2.337 2.201 4.116 4.391 4.116c2.188 0 3.979-1.779 4.387-4.115l1.113-.271v-.393c0-2.635-1.459-4.887-3.508-5.792C35.041 8.58 44 7.108 44 18.789c0 9.668 4 15.711 4 22.134c0 11.565-7.715 17.143-16 17.143" fill="currentColor"></path><ellipse cx="39" cy="16.969" rx="2" ry="3" fill="#000000"></ellipse><path d="M24.998 13.969c-1.102 0-1.998 1.344-1.998 2.998c0 1.658.896 3.002 1.998 3.002c1.107 0 2.002-1.344 2.002-3.002c0-1.654-.895-2.998-2.002-2.998" fill="#000000"></path><path d="M32.973 17.543c-.145.166-.031.563.254.889c.281.325.629.455.773.289c.145-.164.031-.563-.252-.888s-.631-.456-.775-.29" fill="#000000"></path><path d="M30.25 17.833c-.285.325-.396..724-.254.888c.146.166.492.036.777-.289c.283-.325.396-.723.254-.889c-.144-.166-.494-.035-.777.29" fill="currentColor"></path></svg>
                            </flux:tooltip>
                        @else
                            <flux:tooltip content="Windows">
                                <svg
                                    fill="currentColor"
                                    class="w-6 h-6"
                                    viewBox="0 0 256 256"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path d="M112,144v51.63672a7.9983,7.9983,0,0,1-9.43115,7.87061l-64-11.63623A8.00019,8.00019,0,0,1,32,184V144a8.00008,8.00008,0,0,1,8-8h64A8.00008,8.00008,0,0,1,112,144ZM109.126,54.2217a7.995,7.995,0,0,0-6.55713-1.729l-64,11.63623A8.00017,8.00017,0,0,0,32,72v40a8.00008,8.00008,0,0,0,8,8h64a8.00008,8.00008,0,0,0,8-8V60.3633A7.99853,7.99853,0,0,0,109.126,54.2217Zm112-20.36377a7.99714,7.99714,0,0,0-6.55713-1.729l-80,14.5459A7.99965,7.99965,0,0,0,128,54.54543V112a8.00008,8.00008,0,0,0,8,8h80a8.00008,8.00008,0,0,0,8-8V40A8.00028,8.00028,0,0,0,221.126,33.85793ZM216,136H136a8.00008,8.00008,0,0,0-8,8v57.45459a7.99967,7.99967,0,0,0,6.56885,7.87061l80,14.5459A8.0001,8.0001,0,0,0,224,216V144A8.00008,8.00008,0,0,0,216,136Z"/>
                                </svg>
                            </flux:tooltip>
                        @endif
                    </div>

                </div>
            </div>

        </flux:card>
        @endforeach
    </div>

    <flux:modal
        variant="flyout"
        wire:model.self="showMachineModal"
        @close="$wire.closeMachineModal()"
        @cancel="$wire.closeMachineModal()"
    >
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Machine Details</flux:heading>
                <flux:text variant="subtle">{{ data_get($selectedMachine, 'ip') }}</flux:text>
            </div>

            <flux:card>
                <pre class="text-sm">{{ json_encode($selectedMachine, JSON_PRETTY_PRINT) }}</pre>
            </flux:card>

            <div class="flex justify-end">
                <flux:modal.close>
                    <flux:button>Close</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>

    <div class="mt-6">
        <flux:card variant="filled">
            <flux:text class="font-semibold">Total: {{ count($machines) }} machines</flux:text>
        </flux:card>
    </div>
</div>
