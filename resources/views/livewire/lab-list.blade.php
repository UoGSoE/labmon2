<div class="grid grid-cols-4 gap-4">
    <div class="font-semibold">
        Lab
    </div>
    <div class="font-semibold">
        Machines
    </div>
    <div class="font-semibold">
        Online
    </div>
    <div class="font-semibold">
        Options
    </div>
    @foreach ($labs as $lab)
    <div>
        <a href="{{ route('lab.show', $lab->id) }}" class="text-blue-500 hover:text-blue-900">
            {{ $lab->name }}
        </a>
    </div>
    <div>
        {{ $lab->members_count }}
    </div>
    <div>
        {{ $lab->online_count }}
    </div>
    <div>
        <button wire:click="toggleGraphable({{$lab->id}})" title="Show {{ $lab->name }} on student graphs" class="@if ($lab->is_on_graphs) bg-blue-500 text-white @endif bg-transparent text-blue-700 font-semibold py-2 px-4 border border-blue-500 rounded">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M1 10h3v10H1V10zM6 0h3v20H6V0zm5 8h3v12h-3V8zm5-4h3v16h-3V4z" /></svg>
        </button>
        <button wire:click="toggleLimitedRemote({{$lab->id}})" title="{{ $lab->name }} Limited remote access" class="@if ($lab->limited_remote_access) bg-blue-500 text-white @endif bg-transparent text-blue-700 font-semibold py-2 px-4 border border-blue-500 rounded">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M16.32 7.1A8 8 0 1 1 9 4.06V2h2v2.06c1.46.18 2.8.76 3.9 1.62l1.46-1.46 1.42 1.42-1.46 1.45zM10 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12zM7 0h6v2H7V0zm5.12 8.46l1.42 1.42L10 13.4 8.59 12l3.53-3.54z" /></svg>
        </button>
        <button wire:click="toggleAlwaysRemote({{$lab->id}})" title="{{ $lab->name }} Always remote access" class="@if ($lab->always_remote_access) bg-blue-500 text-white @endif bg-transparent text-blue-700 font-semibold py-2 px-4 border border-blue-500 rounded">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-1-7.59V4h2v5.59l3.95 3.95-1.41 1.41L9 10.41z" /></svg>
        </button>
    </div>
    @endforeach

</div>
