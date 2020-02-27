<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Lab;
use App\LabStat;
use Faker\Generator as Faker;

$factory->define(LabStat::class, function (Faker $faker) {
    return [
        'lab_id' => function () {
            return factory(Lab::class)->create()->id;
        },
        'machine_total' => $faker->randomNumber(),
        'logged_in_total' => $faker->randomNumber(),
    ];
});
