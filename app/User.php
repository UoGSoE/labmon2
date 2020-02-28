<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_allowed' => 'boolean',
    ];

    public function scopeallowedAccess($query)
    {
        return $query->where('is_allowed', '=', true);
    }

    public function isAllowedAccess()
    {
        return $this->is_allowed;
    }

    public function grantAccess()
    {
        $this->update(['is_allowed' => true]);
    }
}
