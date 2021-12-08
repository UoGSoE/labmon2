<?php

namespace Database\Factories;

use App\Models\Machine;
use Illuminate\Database\Eloquent\Factories\Factory;

class MachineBookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'machine_id' => Machine::factory(),
            'start' => now(),
            'end' => now()->addHour(),
            'guid' => $this->faker->userName(),
        ];
    }
}
