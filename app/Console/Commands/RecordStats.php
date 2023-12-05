<?php

namespace App\Console\Commands;

use App\Models\Lab;
use App\Models\LabStat;
use Illuminate\Console\Command;

class RecordStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'labmon:recordstats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Record the busyness stats for every lab';

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
     */
    public function handle(): void
    {
        Lab::all()->each->recordStats();
        LabStat::where(
            'created_at',
            '<',
            now()->subDays(config('labmon.truncate_stats_days', 180))
        )->delete();
    }
}
