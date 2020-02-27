<?php

namespace Tests\Feature;

use App\Lab;
use App\LabStat;
use App\Machine;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LabStatsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_record_the_busyness_of_every_lab()
    {
        $lab1 = factory(Lab::class)->create();
        $lab2 = factory(Lab::class)->create();
        $lab3 = factory(Lab::class)->create();
        $machines1 = factory(Machine::class, 5)->create(['lab_id' => $lab1->id]);
        $machines2 = factory(Machine::class, 5)->create(['lab_id' => $lab2->id]);
        $machines3 = factory(Machine::class, 5)->create(['lab_id' => $lab3->id]);

        $this->artisan('labmon:recordstats');

        tap(LabStat::all(), function ($stats) use ($lab1, $lab2, $lab3) {
            $this->assertEquals($lab1->id, $stats[0]->lab_id);
            $this->assertEquals($lab1->members()->count(), $stats[0]->machine_total);
            $this->assertEquals($lab1->members()->online()->count(), $stats[0]->logged_in_total);

            $this->assertEquals($lab2->id, $stats[1]->lab_id);
            $this->assertEquals($lab2->members()->count(), $stats[1]->machine_total);
            $this->assertEquals($lab2->members()->online()->count(), $stats[1]->logged_in_total);

            $this->assertEquals($lab3->id, $stats[2]->lab_id);
            $this->assertEquals($lab3->members()->count(), $stats[2]->machine_total);
            $this->assertEquals($lab3->members()->online()->count(), $stats[2]->logged_in_total);
        });
    }

    /** @test */
    public function the_lab_stats_are_truncated_after_N_days()
    {
        config(['labmon.truncate_stats_days' => 2]);
        $stat1 = factory(LabStat::class)->create(['created_at' => now()->subDays(3)]);
        $stat2 = factory(LabStat::class)->create(['created_at' => now()->subDays(2)]);
        $stat3 = factory(LabStat::class)->create(['created_at' => now()->subDays(1)]);

        $this->assertEquals(3, LabStat::count());

        $this->artisan('labmon:recordstats');

        // we should have 5 stats - the 2 old that should have been kept, and 3
        // new ones for each lab that the stat factories created
        $this->assertEquals(5, LabStat::count());
        $this->assertNull(LabStat::find($stat1->id));
    }

    /** @test */
    public function the_recordstats_command_is_registered_with_the_schedular()
    {
        $this->assertCommandIsScheduled('labmon:recordstats');
    }
}
