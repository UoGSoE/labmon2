<?php

namespace Tests\Feature;

use App\Lab;
use App\Machine;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StatsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_get_the_prometheus_metrics_stats()
    {
        $labs = factory(Lab::class, 3)->create();
        $labs->each(function ($lab) {
            factory(Machine::class, 3)->create(['lab_id' => $lab->id]);
        });

        $response = $this->get('/metrics');

        $response->assertOk();
        $response->assertSee($labs->first()->name);
    }
}
