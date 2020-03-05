<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Machine;
use Faker\Generator as Faker;

$factory->define(Machine::class, function (Faker $faker) {
    return [
        'ip' => $faker->ipv4,
        'logged_in' => $faker->boolean(),
        'name' => $faker->domainWord . '.' . $faker->domainName,
        'meta' => [
            'mac' => $faker->macAddress,
            'cpu' => $faker->macProcessor,
            'model' => $faker->randomElement(['Dell 1234', 'HP 456', 'BBC Micro Model B']),
        ],
    ];
});
