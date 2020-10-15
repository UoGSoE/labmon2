<?php

namespace App\Jobs;

use App\Models\Machine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LookupDns implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $machine;

    public function __construct(Machine $machine)
    {
        $this->machine = $machine;
    }

    public function handle()
    {
        $this->machine->lookupDns();
    }
}
