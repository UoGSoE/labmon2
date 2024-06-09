<div x-cloak x-data="{ modalMachine: null }" @updatemachine="modalMachine = $event.detail.machine">
    <div class="grid grid-cols-3 gap-4 pb-4">
        @foreach ($machines as $machine)
        <div class="flex p-4 border relative @if ($machine->logged_in) border-green-500 shadow shadow-lg @else text-gray-600 shadow-inner @endif">
            <div x-data="{ machine: {{ json_encode($machine) }} }">
                <span @click="$dispatch('updatemachine', {machine: machine})" class="mr-2 cursor-pointer">
                    <svg class="w-12 h-12 fill-current @if ($machine->logged_in) text-green-500 hover:text-green-600 @else text-gray-300 hover:text-gray-400 @endif" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M7 13.33a7 7 0 1 1 6 0V16H7v-2.67zM7 17h6v1.5c0 .83-.67 1.5-1.5 1.5h-3A1.5 1.5 0 0 1 7 18.5V17zm2-5.1V14h2v-2.1a5 5 0 1 0-2 0z" /></svg>
                </span>
            </div>
            <button class="absolute top-0 right-0 p-2" wire:click="toggleLocked({{ $machine->id }})">
                @if ($machine->is_locked) Locked @else Unlocked @endif
                <div style="color: #8899dd">
                    @if (str_starts_with($machine->user_agent, 'curl'))
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="40" fill="currentColor" class="bi bi-ubuntu" viewBox="0 0 16 16">
                            <path d="M2.273 9.53a2.273 2.273 0 1 0 0-4.546 2.273 2.273 0 0 0 0 4.547Zm9.467-4.984a2.273 2.273 0 1 0 0-4.546 2.273 2.273 0 0 0 0 4.546ZM7.4 13.108a5.535 5.535 0 0 1-3.775-2.88 3.273 3.273 0 0 1-1.944.24 7.4 7.4 0 0 0 5.328 4.465c.53.113 1.072.169 1.614.166a3.253 3.253 0 0 1-.666-1.9 5.639 5.639 0 0 1-.557-.091Zm3.828 2.285a2.273 2.273 0 1 0 0-4.546 2.273 2.273 0 0 0 0 4.546Zm3.163-3.108a7.436 7.436 0 0 0 .373-8.726 3.276 3.276 0 0 1-1.278 1.498 5.573 5.573 0 0 1-.183 5.535 3.26 3.26 0 0 1 1.088 1.693ZM2.098 3.998a3.28 3.28 0 0 1 1.897.486 5.544 5.544 0 0 1 4.464-2.388c.037-.67.277-1.313.69-1.843a7.472 7.472 0 0 0-7.051 3.745Z"/>
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="40" fill="currentColor" class="bi bi-windows" viewBox="0 0 16 16">
                            <path d="M6.555 1.375 0 2.237v5.45h6.555V1.375zM0 13.795l6.555.933V8.313H0v5.482zm7.278-5.4.026 6.378L16 16V8.395H7.278zM16 0 7.33 1.244v6.414H16V0z"/>
                        </svg>
                    @endif
                </div>

            </button>
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
                    {{ $machine->updated_at->format('d/m/Y H:i') }}
                    <span class="text-sm text-gray-600">
                        ({{ $machine->updated_at->diffForHumans() }})
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div x-show.transition="modalMachine" class="fixed w-full h-full top-0 left-0 flex items-center justify-center">

        <div class="bg-white w-auto mx-auto rounded border-4 shadow-xl z-50 overflow-y-auto">

            <!-- Add margin if you want to see some of the overlay behind the modal-->
            <div class="py-4 text-left px-8">
                <!--Title-->
                <div class="flex justify-between items-center pb-3">
                    <p class="text-2xl font-bold">Details for <span x-text="modalMachine ? modalMachine.ip : ''"></span></span></p>
                </div>

                <!--Body-->
                <pre class="shadow border bg-white px-8 py-4 w-full"><code
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
    <div class="p-4 bg-blue-100 rounded text-gray-600 font-bold tracking-wide">Total : {{ count($machines) }}</div>
</div>
