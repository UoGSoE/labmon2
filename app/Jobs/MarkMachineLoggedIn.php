<?php

namespace App\Jobs;

use App\Models\Machine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MarkMachineLoggedIn implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $ip;

    public $userAgent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $ip, string $userAgent)
    {
        $this->ip = $ip;
        $this->userAgent = $userAgent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $machine = Machine::firstOrCreate(['ip' => $this->ip], ['ip' => $this->ip]);

        $machine->update([
            'user_agent' => $this->userAgent,
            'logged_in' => true,
        ]);

        if (! $machine->name) {
            LookupDns::dispatch($machine);
        }
    }
}
