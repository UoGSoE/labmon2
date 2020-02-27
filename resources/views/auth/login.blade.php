@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center h-screen">
    <div class="shadow-lg w-1/4">
        <div class="bg-blue-500 h-full text-white text-4xl tracking-wide font-bold p-8 rounded-t-lg text-center">
            Labmon
        </div>
        <form method="POST" action="{{ route('login') }}" class="bg-white rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            @if (count($errors))
                @foreach ($errors->all() as $e)
                  {{ $e }}
                @endforeach
            @endif
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    Username
                </label>
                <input name="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" type="text" placeholder="Username">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input name="password" class="shadow appearance-none border border-red-500 rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" placeholder="******************">
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                    Sign In
                </button>
            </div>
        </form>
    </div>
</div>

@endsection