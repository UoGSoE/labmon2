<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $guarded = [];

    protected $casts = [
        'logged_in' => 'boolean',
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

    public function lookupDns()
    {
        $this->update([
            'name' => gethostbyaddr($this->ip) ?? 'N/A',
        ]);
    }
}
