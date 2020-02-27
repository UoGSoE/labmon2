@extends('layouts.app')

@section('content')

<div class="border-b border-blue-300 mb-4 flex justify-between items-center">
    <h1 class="text-3xl">Lab List</h1>
    @livewire('new-lab-editor')
</div>
@livewire('lab-list')

@endsection