<div class="grid grid-cols-3 gap-4">
    @foreach ($machines as $machine)
    <div class="flex p-4 border @if ($machine->logged_in) border-green-500 shadow shadow-lg @else text-gray-600 shadow-inner @endif">
        <span class="mr-2">
            <svg class="w-12 h-12 fill-current @if ($machine->logged_in) text-green-500 @else text-gray-300 @endif" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M7 13.33a7 7 0 1 1 6 0V16H7v-2.67zM7 17h6v1.5c0 .83-.67 1.5-1.5 1.5h-3A1.5 1.5 0 0 1 7 18.5V17zm2-5.1V14h2v-2.1a5 5 0 1 0-2 0z" /></svg>
        </span>
        <div class="overflow-hidden">
            <div class="font-semibold tracking-wide">
                {{ $machine->ip }}
            </div>
            @if ($machine->name)
            <div class="text-gray-600 font-light tracking-wide">
                {{ $machine->name }}
            </div>
            @endif
            <div class="text-gray-600 font-light tracking-wide">
                {{ $machine->updated_at->format('d/m/Y H:i')}}
                <span class="text-sm text-gray-600">
                    ({{ $machine->updated_at->diffForHumans() }})
                </span>
            </div>
        </div>
    </div>
    @endforeach
</div>