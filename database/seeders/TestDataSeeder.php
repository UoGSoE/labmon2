<?php

namespace Database\Seeders;

use App\Models\Lab;
use App\Models\Machine;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $labs = Lab::factory()->count(120)->create();
        $machines = Machine::factory()->count(250)->create(['user_agent' => 'PowerShell']);
        $machines = $machines->merge(Machine::factory()->count(250)->create(['user_agent' => 'curl']));
        $machines->each(function ($machine) use ($labs) {
            $machine->lab_id = $labs->random()->id;
            $machine->save();
        });
        User::factory()->create([
            'username' => 'billy',
            'is_allowed' => true,
            'password' => bcrypt('hellokitty'),
        ]);
        option(['remote-start-hour' => 19]);
        option(['remote-end-hour' => 7]);
        option(['remote-start-xmas' => '20/Dec']);
        option(['remote-end-xmas' => '05/Jan']);
        option(['remote-start-easter' => '04/Apr']);
        option(['remote-end-easter' => '10/Apr']);
        option(['remote-start-summer' => '04/Jul']);
        option(['remote-end-summer' => '10/Sep']);
    }
}
