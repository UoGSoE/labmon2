<x-layouts.app>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <flux:card class="space-y-6">
            <div>
                <flux:heading size="lg">Edit Members of Lab {{ $lab->name }}</flux:heading>
                <flux:subheading>Provide one IP per line.</flux:subheading>
            </div>

            <form method="POST" action="{{ route('lab.members.update', $lab->id) }}" class="space-y-6">
                @csrf

                <flux:textarea
                    name="ips"
                    label="IP addresses"
                    rows="12"
                    placeholder="10.0.0.12\n10.0.0.13"
                >{{ $lab->members->pluck('ip')->join("\n") }}</flux:textarea>

                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary">Update</flux:button>
                    <flux:button href="{{ route('lab.show', $lab->id) }}" variant="ghost">Cancel</flux:button>
                </div>
            </form>
        </flux:card>
    </div>
</x-layouts.app>
