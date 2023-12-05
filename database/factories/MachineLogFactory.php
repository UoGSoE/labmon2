<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MachineLogFactory extends Factory
{
    public function definition()
    {
        return [
            'ip' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'logged_in' => $this->faker->boolean(),
        ];
    }
}
