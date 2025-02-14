<?php

namespace App\Models;

use App\Jobs\LookupDns;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Lab extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'always_remote_access' => 'boolean',
            'limited_remote_access' => 'boolean',
        ];
    }

    public function members(): HasMany
    {
        return $this->hasMany(Machine::class);
    }

    public function stats(): HasMany
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

    public function replaceExistingMembers(Collection $ipList): void
    {
        $this->removeExistingMembers();
        $this->storeMembers($ipList);
    }

    public function removeExistingMembers(): void
    {
        $this->members->each(function ($machine) {
            $machine->update(['lab_id' => null]);
        });
    }

    public function storeMembers(Collection $ipList): void
    {
        $ipList->each(function ($ip) {
            $machine = Machine::firstOrCreate([
                'ip' => $ip,
            ]);
            $machine->update(['lab_id' => $this->id]);
            LookupDns::dispatch($machine);
        });
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
        return $this->members()->offline()->unlocked()->inRandomOrder()->get();
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
        return ! ($this->always_remote_access || $this->limited_remote_access);
    }
}
