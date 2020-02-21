@extends('layouts.app')

@section('content')

<div class=" border-b border-blue-300 mb-8 flex justify-between items-center">
    <h1 class="text-3xl">Lab {{ $lab->name }}</h1>
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

@endsection