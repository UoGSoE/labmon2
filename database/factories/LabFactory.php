<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Lab;
use Faker\Generator as Faker;

$factory->define(Lab::class, function (Faker $faker) {
    return [
        'name' => $faker->randomElement(['Rankine', 'JWS']) . $faker->randomNumber(3),
        'is_on_graphs' => $faker->boolean(),
        'always_remote_access' => $faker->boolean(),
        'limited_remote_access' => $faker->boolean(),
    ];
});
