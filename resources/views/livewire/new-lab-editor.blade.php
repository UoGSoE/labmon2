<div>
    @if (!$editing)
    <a href="" wire:click.prevent="$set('editing', true)" class="text-blue-500 hover:text-blue-900">Add new lab</a>
    @else
    <span class="flex pb-2">
        @error('labName') <span class="error">{{ $message }}</span> @enderror
        <input wire:model.live="labName" wire:keydown.enter="saveLab" class=" shadow appearance-none mr-2 border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="labName" type="text" placeholder="Lab name...">
        <select wire:model.live="school" class=" shadow appearance-none border rounded w-full py-2 mr-2 bg-white px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <option value="">Select school</option>
            @foreach (config('labmon.schools', []) as $school)
                <option value="{{ $school }}">{{ $school }}</option>
            @endforeach
        </select>
        {{-- <input wire:model.live="school" wire:keydown.enter="saveLab" class=" shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="school" type="text" placeholder="School name..."> --}}
        <button wire:click="saveLab" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
            Save
        </button>
    </span>
    @endif
</div>
