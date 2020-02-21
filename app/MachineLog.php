<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MachineLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'logged_in' => 'boolean',
    ];
}
