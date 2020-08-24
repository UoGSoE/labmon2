<?php

namespace App\Console\Commands;

use App\Jobs\LookupDns;
use App\Machine;
use Illuminate\Console\Command;

class RefreshDnsNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'labmon:refreshdns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the DNS names of all the machines in the db';

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
    public function handle()
    {
        Machine::all()->each(function($machine) {
            LookupDns::dispatch($machine);
        });
    }
}
