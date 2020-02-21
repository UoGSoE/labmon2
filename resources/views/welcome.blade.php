@extends('layouts.app')

@section('content')

<div class="border-b border-blue-300 mb-4 flex justify-between items-center">
    <h1 class="text-3xl">Lab List</h1>
    <a href="" class="text-blue-500 hover:text-blue-900">
        Add new lab
    </a>
</div>
@livewire('lab-list')

@endsection