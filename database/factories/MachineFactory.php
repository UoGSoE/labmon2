<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MachineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ip' => $this->faker->ipv4(),
            'logged_in' => $this->faker->boolean(),
            'name' => $this->faker->domainWord().'.'.$this->faker->domainName(),
            'meta' => [
                'mac' => $this->faker->macAddress(),
                'cpu' => $this->faker->macProcessor(),
                'model' => $this->faker->randomElement(['Dell 1234', 'HP 456', 'BBC Micro Model B']),
            ],
        ];
    }
}
