<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\MachineLog;
use Faker\Generator as Faker;

$factory->define(MachineLog::class, function (Faker $faker) {
    return [
        'ip' => $faker->ipv4,
        'user_agent' => $faker->userAgent,
        'logged_in' => $faker->boolean(),
    ];
});
