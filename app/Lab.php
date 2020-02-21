<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lab extends Model
{
    protected $guarded = [];

    public function members()
    {
        return $this->hasMany(Machine::class);
    }
}
