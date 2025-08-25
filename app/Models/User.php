<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory;
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_allowed' => 'boolean',
        ];
    }

    #[Scope]
    protected function allowedAccess($query)
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
                    $user = new User;
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
