<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'logged_in' => 'boolean',
    ];
}
