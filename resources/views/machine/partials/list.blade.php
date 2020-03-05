<div x-cloak x-data="{ modalMachine: null }" @updatemachine="modalMachine = $event.detail.machine">
    <div class="grid grid-cols-3 gap-4">
        @foreach ($machines as $machine)
        <div class="flex p-4 border @if ($machine->logged_in) border-green-500 shadow shadow-lg @else text-gray-600 shadow-inner @endif">
            <div x-data="{ machine: {{ json_encode($machine) }} }">
                <span @click="$dispatch('updatemachine', {machine: machine})" class="mr-2 cursor-pointer">
                    <svg class="w-12 h-12 fill-current @if ($machine->logged_in) text-green-500 @else text-gray-300 @endif" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M7 13.33a7 7 0 1 1 6 0V16H7v-2.67zM7 17h6v1.5c0 .83-.67 1.5-1.5 1.5h-3A1.5 1.5 0 0 1 7 18.5V17zm2-5.1V14h2v-2.1a5 5 0 1 0-2 0z" /></svg>
                </span>
            </div>
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
    <div x-show.transition="modalMachine" class="fixed w-full h-full top-0 left-0 flex items-center justify-center">

        <div class="bg-gray-100 w-auto mx-auto rounded border-4 shadow-xl z-50 overflow-y-auto">

            <!-- Add margin if you want to see some of the overlay behind the modal-->
            <div class="py-4 text-left px-6">
                <!--Title-->
                <div class="flex justify-between items-center pb-3">
                    <p class="text-2xl font-bold">Details for <span x-text="modalMachine ? modalMachine.ip : ''"></span></span></p>
                </div>

                <!--Body-->
                <pre class="shadow border bg-white p-4 w-full"><code
                      class="language-json"
                      x-html="modalMachine ? Prism.highlight(JSON.stringify(modalMachine, null, 2), Prism.languages.javascript, 'javascript') : ''"
                    >
                </code></pre>

                <!--Footer-->
                <div class="flex justify-end pt-2">
                    <button @click="modalMachine = null" class="modal-close px-4 bg-blue-500 p-3 rounded-lg text-white hover:bg-blue-400">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>