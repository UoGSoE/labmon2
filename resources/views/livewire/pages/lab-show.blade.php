<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            {{-- Lab Name Editor --}}
            <div>
                @if (!$editing)
                    <div class="flex items-center gap-2">
                        <flux:heading size="xl">Lab {{ $labName }}</flux:heading>
                        <flux:button
                            wire:click="$set('editing', true)"
                            variant="ghost"
                            size="sm"
                            icon="pencil"
                            tooltip="Edit lab name"
                            inset="top bottom"
                        />
                    </div>
                @else
                    <div class="space-y-4">
                        <div class="flex gap-4 items-end">
                            <div class="flex-1">
                                <flux:input
                                    wire:model.live="labName"
                                    wire:keydown.enter="updateLabName"
                                    label="Lab Name"
                                    placeholder="Enter lab name"
                                />
                            </div>

                            <div class="flex-1">
                                <flux:select wire:model.live="school" label="School">
                                    <option value="">Select school</option>
                                    @foreach (config('labmon.schools', []) as $school)
                                        <option value="{{ $school }}">{{ $school }}</option>
                                    @endforeach
                                </flux:select>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <flux:button wire:click="updateLabName" variant="primary">
                                Update
                            </flux:button>
                            <flux:button wire:click="deleteLab" variant="danger">
                                {{ $deleteButtonText }}
                            </flux:button>
                            <flux:button wire:click="$set('editing', false)" variant="subtle">
                                Cancel
                            </flux:button>
                        </div>
                    </div>
                @endif
            </div>

            <flux:button variant="subtle" href="{{ route('lab.members.edit', $lab->id) }}" icon="pencil">
                Edit Members
            </flux:button>
        </div>
        <flux:separator class="mt-4" />
    </div>

    <div class="max-w-7xl mx-auto">
        @include('livewire.partials.machine-cards')
    </div>
</div>
