<div>
    @if (!$editing)
    <h1 class="text-3xl flex items-center pb-1">
        <span class="mr-2">
            Lab {{ $labName }}
        </span>
        <a wire:click="$set('editing', true)" title="Edit lab name" class="text-blue-500 hover:text-blue-900">
            <svg class="fill-current w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M12.3 3.7l4 4L4 20H0v-4L12.3 3.7zm1.4-1.4L16 0l4 4-2.3 2.3-4-4z" /></svg>
        </a>
    </h1>
    @else
    <span class="flex pb-2">
        <input wire:model="labName" wire:keydown.enter="updateLabName" class=" shadow mr-2 appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" type="text" x-model="lab.name">
        <select wire:model="school" class=" shadow appearance-none border rounded w-full py-2 mr-2 bg-white px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <option value="">Select school</option>
            @foreach (config('labmon.schools', []) as $school)
                <option value="{{ $school }}">{{ $school }}</option>
            @endforeach
        </select>
        <button wire:click="updateLabName" class="mr-8 bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
            Update
        </button>
        <button wire:click="deleteLab" class="bg-transparent hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded">
            {{ $deleteButtonText }}
        </button>
    </span>
    @endif
</div>
