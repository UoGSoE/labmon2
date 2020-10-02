<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LabStatFactory extends Factory
{
    protected $model = \App\LabStat::class;

    public function definition()
    {
        return [
            'lab_id' => \App\Lab::factory(),
            'machine_total' => $this->faker->randomNumber(),
            'logged_in_total' => $this->faker->randomNumber(),
        ];
    }
}
