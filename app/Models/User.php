<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
                $guid = strtolower(trim($guid));
                $user = static::where('username', '=', $guid)->first();
                if (! $user) {
                    $ldapUser = \Ldap::findUser($guid);
                    if (! $ldapUser) {
                        return;
                    }
                    $user = new User();
                    $user->username = $guid;
                    $user->surname = $ldapUser->surname;
                    $user->forenames = $ldapUser->forenames;
                    $user->email = $ldapUser->email;
                    $user->password = bcrypt(Str::random(64));
                    $user->is_staff = true;
                    $user->save();
                }
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
