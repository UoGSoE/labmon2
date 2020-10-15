<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    use HasFactory;

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

    public static function setAllowedUsers(self $user, string $guidList): void
    {
        static::where('username', '!=', $user->username)
            ->update(['is_allowed' => false]);

        collect(explode("\r\n", $guidList))
            ->filter()
            ->each(function ($guid) {
                $user = static::where('username', '=', $guid)->first();
                if ($user) {
                    $user->grantAccess();
                }
            });
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
