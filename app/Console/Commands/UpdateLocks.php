<?php

namespace App\Console\Commands;

use App\Models\Machine;
use Illuminate\Console\Command;

class UpdateLocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'labmon:update-locks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lock and unlock machines which are booked or finished up';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Machine::unlocked()
            ->whereHas('bookings', fn ($query) => $query->where('start', '<=', now())->where('end', '>=', now()))
            ->update(['is_locked' => true]);

        Machine::locked()
            ->whereDoesntHave('bookings', fn ($query) => $query->where('start', '<=', now())->where('end', '>=', now()))
            ->update(['is_locked' => false]);

        return Command::SUCCESS;
    }
}
