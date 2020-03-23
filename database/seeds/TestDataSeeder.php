<?php

use App\Lab;
use App\Machine;
use App\User;
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
        $labs = factory(Lab::class, 20)->create();
        $machines = factory(Machine::class, 500)->create();
        $machines->each(function ($machine) use ($labs) {
            $machine->lab_id = $labs->random()->id;
            $machine->save();
        });
        factory(User::class)->create([
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
