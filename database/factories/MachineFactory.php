<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Machine;
use Faker\Generator as Faker;

$factory->define(Machine::class, function (Faker $faker) {
    return [
        'ip' => $faker->ipv4,
        'logged_in' => $faker->boolean(),
        'name' => $faker->domainWord . '.' . $faker->domainName,
    ];
});
