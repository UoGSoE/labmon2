<?php

return [

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'ldapusers',
        ],

        'api' => [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => false,
        ],
    ],

    'providers' => [
        'ldapusers' => [
            'driver' => 'ldapeloquent',
            'model' => App\Models\User::class,
        ],
    ],

];
