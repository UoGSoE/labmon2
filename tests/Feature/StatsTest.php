<?php

use App\Models\Lab;
use App\Models\Machine;

test('we can get the prometheus metrics stats', function () {
    $this->withoutExceptionHandling();
    $labs = Lab::factory()->count(3)->create();
    $labs->each(function ($lab) {
        Machine::factory()->count(3)->create(['lab_id' => $lab->id]);
    });

    $response = $this->get('/metrics');

    $response->assertOk();
    $response->assertSee($labs->first()->name);
});
