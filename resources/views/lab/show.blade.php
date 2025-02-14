@extends('components.layouts.app')

@section('content')

<div class=" border-b border-blue-300 mb-8 flex justify-between items-center" x-data='{lab: @json($lab), editing: false}'>
    @livewire('lab-name-editor', ['lab' => $lab])
    <a href="{{ route('lab.members.edit', $lab->id) }}" class="text-blue-500 hover:text-blue-900">
        Edit Members
    </a>
</div>

@livewire('machine-list', ['machines' => $lab->members()->orderBy('ip')->get(), 'labId' => $lab->id])

@endsection
