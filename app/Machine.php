<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Machine extends Model
{
    protected $guarded = [];

    protected $casts = [
        'logged_in' => 'boolean',
        'is_locked' => 'boolean',
        'meta' => 'array',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }

    public function scopeOnline($query)
    {
        return $query->where('logged_in', '=', true);
    }

    public function scopeOffline($query)
    {
        return $query->where('logged_in', '=', false);
    }

    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', '=', false);
    }

    public function scopeLocked($query)
    {
        return $query->where('is_locked', '=', true);
    }

    public function lookupDns()
    {
        if (config('labmon.dns_server')) {
            $this->updateHostnameViaShell();
            return;
        }
        $this->update([
            'name' => gethostbyaddr($this->ip) ?? null,
        ]);
    }

    protected function updateHostnameViaShell()
    {
        // see AppServiceProvider for this binding/alias and
        // https://laracasts.com/discuss/channels/testing/how-to-test-symfony-process-inside-a-queued-job?page=1#reply=447955
        // for the reasons
        $process = app('App\Process', ['host', $this->ip, config('labmon.dns_server')]);
        $process->run();
        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        $lines = explode("\n", $output);
        array_pop($lines); // remove last trailing lines which are a trailing '\n'
        $parts = explode(' ', array_pop($lines)); // grab & split the last line of the host lookup output
        if (count($parts) != 5) {
            throw new Exception('DNS host lookup failed on for output of ' . $output);
        }

        $name = substr($parts[4], 0, -1); // output ends up as 'host.example.com.\n' - so strip trailing chars
        $this->update([
            'name' => $name,
        ]);
    }

    public function toggleLocked()
    {
        $this->update([
            'is_locked' => ! $this->is_locked,
        ]);
    }
}
