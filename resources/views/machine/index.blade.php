@extends('components.layouts.app')

@section('content')

    @livewire('machine-list', ['machines' => $machines])

@endsection
