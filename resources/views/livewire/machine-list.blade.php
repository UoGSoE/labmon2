<div>
    <div class="mb-4 flex items-center">
        <input wire:model.live="filter" autocomplete="off" class="mr-4 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" type="text" placeholder="Filter...">
        <label class="flex-auto whitespace-no-wrap">
            <input class="" type="checkbox" wire:model.live="includeMeta" value="1"> Search metadata?
        </label>
    </div>
    <div class="mb-4 text-gray-600">
        <label class="mr-2">
            <input type="radio" name="statusFilter" id="statusFilterNone" wire:model.live="statusFilter" value=""> All
        </label>
        <label class="mr-2">
            <input type="radio" name="statusFilter" id="statusFilterLoggedIn" wire:model.live="statusFilter" value="logged_in"> Logged In
        </label>
        <label class="mr-2">
            <input type="radio" name="statusFilter" id="statusFilterNotLoggedIn" wire:model.live="statusFilter" value="not_logged_in"> Not Logged In
        </label>
        <label class="mr-2">
            <input type="radio" name="statusFilter" id="statusFilterLocked" wire:model.live="statusFilter" value="locked"> Locked
        </label>
        <label class="mr-2">
            <input type="radio" name="statusFilter" id="statusFilterNotLocked" wire:model.live="statusFilter" value="not_locked"> Not Locked
        </label>
        <span class="mr-2"> | </span>
        <label for="" class="mr-2">
            <input type="radio" name="osFilter" id="osFilterAny" wire:model.live="osFilter" value=""> Any OS
        </label>
        <label for="" class="mr-2">
            <input type="radio" name="osFilter" id="osFilterWindows" wire:model.live="osFilter" value="Power"> Windows
        </label>
        <label for="" class="mr-2">
            <input type="radio" name="osFilter" id="osFilterLinux" wire:model.live="osFilter" value="curl"> Linux
        </label>
    </div>
    @include('machine.partials.list')
</div>
