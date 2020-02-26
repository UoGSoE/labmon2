@extends('layouts.app')

@section('content')

<h1 class="text-3xl flex items-center border-b border-blue-300 mb-4">
    Options
</h1>

<form action="" method="POST">
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
            Start &amp; end dates for summer holidays
        </label>
        <div class="flex">
            <input name="remote-summer" value="{{ option('remote-start-summer', '01/Jun') }} - {{ option('remote-end-summer', '31/Aug') }}" class="pikaday shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-4" id="username" type="text" min="1" max="23">
        </div>
        </div>
        <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
            Start &amp; end dates for xmas holidays
        </label>
        <div class="flex">
        <input name="remote-xmas" value="{{ option('remote-start-xmas', '01/June') }} - {{ option('remote-end-xmas', '31/August') }}" class="pikaday shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-4" id="username" type="text" min="1" max="23">
        </div>
        </div>
        <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
            Start &amp; end dates for easter holidays
        </label>
        <div class="flex">
        <input name="remote-easter" value="{{ option('remote-start-easter', '01/June') }} - {{ option('remote-end-easter', '31/August') }}" class="pikaday shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-4" id="username" type="text" min="1" max="23">
        </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    window.addEventListener('load', function () {
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