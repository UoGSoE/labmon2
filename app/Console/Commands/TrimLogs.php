<?php

namespace App\Console\Commands;

use App\Models\Machine;
use Illuminate\Console\Command;

class TrimLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'labmon:trimlogs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trim all the logs';

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
     * @return mixed
     */
    public function handle(): void
    {
        Machine::where(
            'updated_at',
            '<',
            now()->subDays(config('labmon.machine_log_days', 6 * 30))
        )->delete();
    }
}
