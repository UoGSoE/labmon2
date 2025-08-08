<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <flux:heading size="xl">Lab List</flux:heading>

            {{-- New Lab Creation Form --}}
            <div>
                @if (!$editing)
                    <flux:button wire:click="$set('editing', true)" variant="primary" icon="plus">
                        Add new lab
                    </flux:button>
                @else
                    <div class="space-y-4">
                        @error('labName')
                            <flux:text variant="danger">{{ $message }}</flux:text>
                        @enderror

                        <div class="flex gap-4 items-end">
                            <div class="flex-1">
                                <flux:input
                                    wire:model.live="labName"
                                    wire:keydown.enter="saveLab"
                                    label="Lab Name"
                                    placeholder="Lab name..."
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

                            <div class="flex gap-2">
                                <flux:button wire:click="saveLab" variant="primary">
                                    Save
                                </flux:button>
                                <flux:button wire:click="$set('editing', false)" variant="subtle">
                                    Cancel
                                </flux:button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <flux:separator class="mt-4" />
    </div>

    {{-- Lab List Table --}}
    <div class="max-w-6xl mx-auto">
        <div class="mb-6">
            <flux:heading>Laboratories</flux:heading>
            <flux:text class="mt-2">Manage laboratory computers and their settings.</flux:text>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Lab</flux:table.column>
                <flux:table.column>Machines</flux:table.column>
                <flux:table.column>Online</flux:table.column>
                <flux:table.column align="end">Options</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($labs as $lab)
                <flux:table.row :key="$lab->id">
                    <flux:table.cell>
                        <flux:link href="{{ route('lab.show', $lab->id) }}">{{ $lab->name }}</flux:link>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:text>{{ $lab->members_count }}</flux:text>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:text>{{ $lab->online_count }}</flux:text>
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex">
                           <flux:spacer />
                        <flux:button.group>
                            <flux:button
                                wire:click="toggleGraphable({{$lab->id}})"
                                variant="{{ $lab->is_on_graphs ? 'primary' : 'subtle' }}"
                                size="sm"
                                icon="chart-bar"
                                tooltip="Show {{ $lab->name }} on student graphs"
                                inset="top bottom"
                            />
                            <flux:button
                                wire:click="toggleLimitedRemote({{$lab->id}})"
                                variant="{{ $lab->limited_remote_access ? 'primary' : 'subtle' }}"
                                size="sm"
                                icon="clock"
                                tooltip="{{ $lab->name }} Limited remote access"
                                inset="top bottom"
                            />
                            <flux:button
                                wire:click="toggleAlwaysRemote({{$lab->id}})"
                                variant="{{ $lab->always_remote_access ? 'primary' : 'subtle' }}"
                                size="sm"
                                icon="globe-alt"
                                tooltip="{{ $lab->name }} Always remote access"
                                inset="top bottom"
                            />
                        </flux:button.group>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>
