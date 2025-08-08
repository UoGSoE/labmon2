<div class="max-w-4xl mx-auto">
    <flux:card>
        <div class="mb-6">
            <flux:heading>Dashboard</flux:heading>
        </div>

        <flux:text>You are logged in!</flux:text>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:card>
                <flux:heading size="sm" class="mb-4">Labs</flux:heading>
                <flux:text class="mb-4">View and manage laboratory computers.</flux:text>
                <flux:button variant="primary" href="{{ route('lab.index') }}">View Labs</flux:button>
            </flux:card>

            <flux:card>
                <flux:heading size="sm" class="mb-4">Machines</flux:heading>
                <flux:text class="mb-4">Monitor all machines across all labs.</flux:text>
                <flux:button variant="subtle" href="{{ route('machine.index') }}">View All Machines</flux:button>
            </flux:card>
        </div>
    </flux:card>
</div>
