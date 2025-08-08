<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <flux:heading size="xl">Options</flux:heading>
        <flux:separator class="mt-4" />
    </div>

    @if (session('success'))
        <flux:text variant="success" class="mb-6">{{ session('success') }}</flux:text>
    @endif

    <flux:card>
        <form wire:submit="save" class="space-y-6">
            
            <div>
                <flux:heading size="sm" class="mb-4">Remote Access Hours</flux:heading>
                <flux:text class="mb-4">Set the start and end hours for limited remote access.</flux:text>
                <div class="grid grid-cols-2 gap-4">
                    <flux:input 
                        wire:model="remoteStartHour" 
                        label="Start Hour"
                        type="number" 
                        min="0" 
                        max="23"
                    />
                    <flux:input 
                        wire:model="remoteEndHour" 
                        label="End Hour"
                        type="number" 
                        min="0" 
                        max="23"
                    />
                </div>
            </div>

            <div>
                <flux:heading size="sm" class="mb-4">Holiday Periods</flux:heading>
                <flux:text class="mb-4">Configure remote access during holiday periods.</flux:text>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <flux:input 
                            wire:model="remoteSummer" 
                            label="Summer Holidays"
                            description="Current: {{ option('remote-start-summer') }} - {{ option('remote-end-summer') }}"
                            class="pikaday"
                            type="text"
                        />
                    </div>
                    
                    <div>
                        <flux:input 
                            wire:model="remoteXmas" 
                            label="Christmas Holidays"
                            description="Current: {{ option('remote-start-xmas') }} - {{ option('remote-end-xmas') }}"
                            class="pikaday"
                            type="text"
                        />
                    </div>
                    
                    <div>
                        <flux:input 
                            wire:model="remoteEaster" 
                            label="Easter Holidays"
                            description="Current: {{ option('remote-start-easter') }} - {{ option('remote-end-easter') }}"
                            class="pikaday"
                            type="text"
                        />
                    </div>
                </div>
            </div>

            <div>
                <flux:heading size="sm" class="mb-4">Allowed Users</flux:heading>
                <flux:text class="mb-4">GUIDs allowed to access {{ config('app.name') }} (one per line).</flux:text>
                <flux:textarea 
                    wire:model="allowedGuids" 
                    rows="12"
                    class="font-mono"
                />
            </div>

            <flux:separator />
            
            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    Save Settings
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>

@push('scripts')
<script>
    window.addEventListener('load', function() {
        document.querySelectorAll('.pikaday').forEach(el => {
            let picker = new Litepicker({
                element: el,
                numberOfMonths: 2,
                numberOfColumns: 2,
                singleMode: false,
                format: 'DD/MMM'
            });
        });
    });
</script>
@endpush