<div>
    <div class="mb-4 flex items-center">
        <input wire:model="filter" autocomplete="off" class="mr-4 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" type="text" placeholder="Filter...">
        <label class="flex-auto whitespace-no-wrap">
            <input class="" type="checkbox" wire:model="includeMeta" value="1"> Search metadata?
        </label>
    </div>
    @include('machine.partials.list')
</div>