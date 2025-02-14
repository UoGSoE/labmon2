@extends('components.layouts.app')

@section('content')

<div class=" border-b border-blue-300 mb-8 flex justify-between items-center">
    <h1 class="text-3xl">Edit Members of Lab {{ $lab->name }}</h1>
</div>

<form action="{{ route('lab.members.update', $lab->id) }}" method="POST">
    @csrf
    <textarea name="ips" class="mb-8 shadow h-64 appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-loose focus:outline-none focus:shadow-outline">{{ $lab->members->pluck("ip")->join("\n") }}</textarea>
    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Update
    </button>
    <a href="{{ route('lab.show', $lab->id) }}" class="bg-transparent text-blue-700 font-semibold hover:text-blue-500 py-2 px-4 border-transparent rounded">
        Cancel
    </a>
</form>

@endsection
