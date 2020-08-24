<div x-data="{ machine: null }" x-on:openmodal="machine = $event.detail.machine">
    <div x-show="machine" class="fixed w-full h-full top-0 left-0 flex items-center justify-center">
        <div class="absolute w-full h-full bg-gray-900 opacity-50"></div>

        <div class="bg-white w-auto mx-auto rounded shadow-lg z-50 overflow-y-auto">

            <!-- Add margin if you want to see some of the overlay behind the modal-->
            <div class="py-4 text-left px-6">
                <!--Title-->
                <div class="flex justify-between items-center pb-3">
                    <p class="text-2xl font-bold">Details for <span x-text="machine ? machine.ip : ''"></span></span></p>
                </div>

                <!--Body-->
                <pre class="shadow border w-full"><code class="language-json" v-text="machine ? JSON.stringify(machine) : ''"></code></pre>

                <!--Footer-->
                <div class="flex justify-end pt-2">
                    <button @click.prevent="machine = null" class="modal-close px-4 bg-blue-500 p-3 rounded-lg text-white hover:bg-blue-400">Close</button>
                    <button @click.prevent="toggleMachineLocked" class="px-4 bg-blue-500 p-3 rounded-lg text-white hover:bg-blue-400">Mark machine as @if ($machine->locked) available @else locked @endif</button>
                </div>

            </div>
        </div>
    </div>
</div>