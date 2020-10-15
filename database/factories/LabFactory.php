<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LabFactory extends Factory
{
    protected $model = \App\Models\Lab::class;

    public function definition()
    {
        $limited = $this->faker->boolean();
        $always = ! $limited;

        return [
            'name' => $this->faker->randomElement(['Rankine', 'JWS']).' '.$this->faker->randomNumber(3),
            'is_on_graphs' => $this->faker->boolean(),
            'always_remote_access' => $always,
            'limited_remote_access' => $limited,
        ];
    }
}
