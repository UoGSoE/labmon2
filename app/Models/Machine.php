<?php

namespace App\Models;

use Exception;
use Carbon\Carbon;
use TitasGailius\Terminal\Terminal;
use Symfony\Component\Process\Process;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Machine extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'logged_in' => 'boolean',
        'is_locked' => 'boolean',
        'meta' => 'array',
        'locked_from' => 'datetime',
        'locked_until' => 'datetime',
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

    public function scopeUnlockedBetween($query, Carbon $from, Carbon $to)
    {
        return $query->offline()->unlocked()->where(
            fn ($query) =>
                $query->whereNull('locked_from')
                        ->orWhere(
                            fn ($query) =>
                                $query->whereNotBetween('locked_until', [$from->copy()->subMinutes(5), $to->copy()->addMinutes(5)])
                                    ->whereNotBetween('locked_from', [$from->copy()->subMinutes(5), $to->copy()->addMinutes(5)])
                        )
        );
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
        $response = Terminal::run("host {$this->ip} ".config('labmon.dns_server'));
        $hostLine = collect($response->lines())->filter()->last();
        $hostLine = collect(explode("\n", (string) $hostLine))->filter()->last();
        $parts = explode(' ', $hostLine);
        if (count($parts) != 5) {
            throw new Exception('DNS host lookup failed on for output of '.$response->output());
        }

        $name = substr($parts[4], 0, -1); // output ends up as 'host.example.com.\n' - so strip trailing chars
        $this->update([
            'name' => $name,
        ]);
    }

    public function lockFor(string $guid, Carbon $from, Carbon $to)
    {
        $this->update([
            'locked_from' => $from,
            'locked_until' => $to,
            'locked_for' => $guid,
            'is_locked' => true,
        ]);
    }

    public function toggleLocked()
    {
        $this->update([
            'is_locked' => ! $this->is_locked,
        ]);
    }

    public function isLocked(): bool
    {
        return (bool) $this->is_locked;
    }

    public function isNotLocked(): bool
    {
        return ! $this->isLocked();
    }
}
