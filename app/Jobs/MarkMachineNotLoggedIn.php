<?php

namespace App\Jobs;

use App\Models\Machine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MarkMachineNotLoggedIn implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $ip;

    public $userAgent;

    public $meta;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $ip, string $userAgent, $meta = [])
    {
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->meta = $meta;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $machine = Machine::firstOrCreate(['ip' => $this->ip], [
            'user_agent' => $this->userAgent,
        ]);

        $machine->update([
            'meta' => $this->meta ?? null,
        ]);

        if (! $machine->name) {
            LookupDns::dispatch($machine);
        }
    }
}
