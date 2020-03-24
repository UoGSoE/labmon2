<div>
    <div class="mb-4 flex items-center">
        <input wire:model="filter" autocomplete="off" class="mr-4 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" type="text" placeholder="Filter...">
        <label class="flex-auto whitespace-no-wrap">
            <input class="" type="checkbox" wire:model="includeMeta" value="1"> Search metadata?
        </label>
    </div>
    <div class="mb-4 text-gray-600">
        <label class="mr-2">
            <input type="radio" name="statusFilter" id="statusFilterNone" wire:model="statusFilter" value=""> All
        </label>
        <label class="mr-2">
            <input type="radio" name="statusFilter" id="statusFilterLoggedIn" wire:model="statusFilter" value="logged_in"> Logged In
        </label>
        <label class="mr-2">
            <input type="radio" name="statusFilter" id="statusFilterNotLoggedIn" wire:model="statusFilter" value="not_logged_in"> Not Logged In
        </label>
        <label class="mr-2">
            <input type="radio" name="statusFilter" id="statusFilterLocked" wire:model="statusFilter" value="locked"> Locked
        </label>
        <label class="mr-2">
            <input type="radio" name="statusFilter" id="statusFilterNotLocked" wire:model="statusFilter" value="not_locked"> Not Locked
        </label>
    </div>
    @include('machine.partials.list')
</div>