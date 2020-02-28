<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Lab extends Model
{
    protected $guarded = [];

    protected $casts = [
        'always_remote_access' => 'boolean',
        'limited_remote_access' => 'boolean',
    ];

    public function members()
    {
        return $this->hasMany(Machine::class);
    }

    public function stats()
    {
        return $this->hasMany(LabStat::class);
    }

    public function scopeGraphable($query)
    {
        return $query->where('is_on_graphs', '=', true);
    }

    public function scopeAlwaysRemote($query)
    {
        return $query->where('always_remote_access', '=', true);
    }

    public function scopeAnyRemote($query)
    {
        return $query->where('always_remote_access', '=', true)
            ->orWhere('limited_remote_access', '=', true);
    }

    public function scopeAvailableForRdp($query)
    {
        if ($this->isntAHolidayPeriod() and $this->isInWorkHours()) {
            return $query->alwaysRemote();
        }
        return $query->anyRemote();
    }

    public function recordStats()
    {
        $this->stats()->create([
            'machine_total' => $this->members()->count(),
            'logged_in_total' => $this->members()->online()->count(),
        ]);
    }

    public function getAvailableRdpMachines()
    {
        if ($this->alwaysAllowsRdp()) {
            return $this->getOfflineMachines();
        }
        if ($this->doesntAllowRdp()) {
            return collect([]);
        }
        if ($this->isntAHolidayPeriod() and $this->isInWorkHours()) {
            return collect([]);
        }
        return $this->getOfflineMachines();
    }

    public function getOfflineMachines()
    {
        return $this->members()->offline()->inRandomOrder()->get();
    }

    public function isInWorkHours()
    {
        return now()->hour < option('remote-start-hour') && now()->hour > option('remote-end-hour');
    }

    public function isntAHolidayPeriod()
    {
        $easterStart = Carbon::createFromFormat('d/M', option('remote-start-easter', '01/Apr'));
        $easterEnd = Carbon::createFromFormat('d/M', option('remote-end-easter', '14/Apr'));
        $summerStart = Carbon::createFromFormat('d/M', option('remote-start-summer', '01/Jul'));
        $summerEnd = Carbon::createFromFormat('d/M', option('remote-end-summer', '01/Sep'));
        $xmasStart = Carbon::createFromFormat('d/M', option('remote-start-xmas', '20/Dec'));
        $xmasEnd = Carbon::createFromFormat('d/M', option('remote-end-xmas', '05/Jan'));

        return ! (
            now()->between($easterStart, $easterEnd) ||
            now()->between($summerStart, $summerEnd) ||
            now()->between($xmasStart, $xmasEnd)
        );
    }

    public function alwaysAllowsRdp()
    {
        return $this->always_remote_access;
    }

    public function doesntAllowRdp()
    {
        return !($this->always_remote_access || $this->limited_remote_access);
    }
}
