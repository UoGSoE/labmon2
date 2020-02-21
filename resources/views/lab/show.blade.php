@extends('layouts.app')

@section('content')

<div class=" border-b border-blue-300 mb-8 flex justify-between items-center" x-data='{lab: @json($lab), editing: false}'>
    <h1 class="text-3xl flex items-center" x-show="!editing">
        <span class="mr-2">
            Lab {{ $lab->name }}
        </span>
        <a @click="editing = true" class="text-blue-500 hover:text-blue-900">
            <svg class="fill-current w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M12.3 3.7l4 4L4 20H0v-4L12.3 3.7zm1.4-1.4L16 0l4 4-2.3 2.3-4-4z" /></svg>
        </a>
    </h1>
    <span x-show="editing" class="flex">
        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" type="text" x-model="lab.name">
        <button @click="submit(lab)" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
            Update
        </button>
    </span>
    <a href="{{ route('lab.members.edit', $lab->id) }}" class="text-blue-500 hover:text-blue-900">
        Edit Members
    </a>
</div>

<div class="grid grid-cols-4 gap-4">
    @foreach ($lab->members as $machine)
    <div class="flex shadow p-4">
        <span>
            <svg class="w-12 h-12 fill-current @if ($machine->logged_in) text-green-500" @else text-gray-300 @endif" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M7 13.33a7 7 0 1 1 6 0V16H7v-2.67zM7 17h6v1.5c0 .83-.67 1.5-1.5 1.5h-3A1.5 1.5 0 0 1 7 18.5V17zm2-5.1V14h2v-2.1a5 5 0 1 0-2 0z" /></svg>
        </span>
        <div>
            <div>
                {{ $machine->ip }}
            </div>
            <div>
                {{ $machine->updated_at->format('d/m/Y H:i')}}
            </div>
        </div>
    </div>
    @endforeach
</div>
<script>
    function submit(lab) {
        console.log(lab);
    }
</script>
@endsection
