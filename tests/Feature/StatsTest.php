<?php

namespace Tests\Feature;

use App\Lab;
use App\Machine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StatsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_get_the_prometheus_metrics_stats()
    {
        $this->withoutExceptionHandling();
        $labs = Lab::factory()->count(3)->create();
        $labs->each(function ($lab) {
            Machine::factory()->count(3)->create(['lab_id' => $lab->id]);
        });

        $response = $this->get('/metrics');

        $response->assertOk();
        $response->assertSee($labs->first()->name);
    }
}
