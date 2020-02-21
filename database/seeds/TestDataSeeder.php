<?php

use App\Lab;
use App\Machine;
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
        $machines = factory(Machine::class, 300)->create();
        $machines->each(function ($machine) use ($labs) {
            $machine->lab_id = $labs->random()->id;
            $machine->save();
        });
    }
}
