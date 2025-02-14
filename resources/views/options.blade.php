@extends('components.layouts.app')

@section('content')

<h1 class="text-3xl flex items-center border-b border-blue-300 mb-4">
    Options
</h1>

<form action="{{ route('options.update') }}" method="POST">
    @csrf
    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
            Start &amp; end hour for limited remote access
        </label>
        <div class="flex">
            <input name="remote-start-hour" value="{{ option('remote-start-hour', 19) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-4" id="start-hour" type="number" min="1" max="23">
            <input name="remote-end-hour" value="{{ option('remote-end-hour', 6) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" type="number" min="1" max="23">
        </div>
    </div>
    <div class="flex justify-between">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                Start &amp; end dates for summer holidays <span class="text-gray-600 font-light tracking-wide">({{ option('remote-start-summer') }} - {{ option('remote-end-summer') }})</span>
            </label>
            <div class="flex">
                <input name="remote-summer" value="" class="pikaday shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-4" id="username" type="text" min="1" max="23">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                Start &amp; end dates for xmas holidays <span class="text-gray-600 font-light tracking-wide">({{ option('remote-start-xmas') }} - {{ option('remote-end-xmas') }})</span>
            </label>
            <div class="flex">
                <input name="remote-xmas" value="" class="pikaday shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-4" id="username" type="text" min="1" max="23">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                Start &amp; end dates for easter holidays <span class="text-gray-600 font-light tracking-wide">({{ option('remote-start-easter') }} - {{ option('remote-end-easter') }})</span>
            </label>
            <div class="flex">
                <input name="remote-easter" value="" class="pikaday shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-4" id="username" type="text" min="1" max="23">
            </div>
        </div>
    </div>

    <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
        GUIDs allowed to access {{ config('app.name') }}
    </label>
    <textarea name="allowed_guids" class="mb-8 shadow h-64 appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-loose focus:outline-none focus:shadow-outline">{{ $allowedUsers->pluck("username")->join("\n") }}</textarea>

    <hr class="mb-4" />
    <button class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
        Save
    </button>
</form>
@endsection

@push('scripts')
<script>
    window.addEventListener('load', function() {
        document.querySelectorAll('.pikaday').forEach(el => {
            let picker = new Litepicker({
                element: el,
                numberOfMonths: 2,
                numberOfColumns: 2,
                singleMode: false,
                format: 'DD/MMM'
            });
        });
    });
</script>
@endpush
