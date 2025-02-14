<?php

use App\Models\Lab;
use App\Models\LabStat;
use App\Models\Machine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


test('we can record the busyness of every lab', function () {
    $lab1 = Lab::factory()->create();
    $lab2 = Lab::factory()->create();
    $lab3 = Lab::factory()->create();
    $machines1 = Machine::factory()->count(5)->create(['lab_id' => $lab1->id]);
    $machines2 = Machine::factory()->count(5)->create(['lab_id' => $lab2->id]);
    $machines3 = Machine::factory()->count(5)->create(['lab_id' => $lab3->id]);

    $this->artisan('labmon:recordstats');

    tap(LabStat::all(), function ($stats) use ($lab1, $lab2, $lab3) {
        expect($stats[0]->lab_id)->toEqual($lab1->id);
        expect($stats[0]->machine_total)->toEqual($lab1->members()->count());
        expect($stats[0]->logged_in_total)->toEqual($lab1->members()->online()->count());

        expect($stats[1]->lab_id)->toEqual($lab2->id);
        expect($stats[1]->machine_total)->toEqual($lab2->members()->count());
        expect($stats[1]->logged_in_total)->toEqual($lab2->members()->online()->count());

        expect($stats[2]->lab_id)->toEqual($lab3->id);
        expect($stats[2]->machine_total)->toEqual($lab3->members()->count());
        expect($stats[2]->logged_in_total)->toEqual($lab3->members()->online()->count());
    });
});

test('the lab stats are truncated after n days', function () {
    config(['labmon.truncate_stats_days' => 2]);
    $stat1 = LabStat::factory()->create(['created_at' => now()->subDays(3)]);
    $stat2 = LabStat::factory()->create(['created_at' => now()->subDays(2)]);
    $stat3 = LabStat::factory()->create(['created_at' => now()->subDays(1)]);

    expect(LabStat::count())->toEqual(3);

    $this->artisan('labmon:recordstats');

    // we should have 5 stats - the 2 old that should have been kept, and 3
    // new ones for each lab that the stat factories created
    expect(LabStat::count())->toEqual(5);
    expect(LabStat::find($stat1->id))->toBeNull();
});

test('the recordstats command is registered with the schedular', function () {
    $this->assertCommandIsScheduled('labmon:recordstats');
});
