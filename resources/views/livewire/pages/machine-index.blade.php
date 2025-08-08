<div class="max-w-7xl mx-auto">
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
                                <flux:icon.computer-desktop class="w-6 h-6" />
                                <flux:text variant="subtle" size="xs" class="mt-1">Linux</flux:text>
                            @else
                                <flux:icon.computer-desktop class="w-6 h-6" />
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