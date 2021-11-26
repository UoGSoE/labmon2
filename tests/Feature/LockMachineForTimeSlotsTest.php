<?php

namespace Tests\Feature;

use App\Models\Lab;
use Tests\TestCase;
use App\Models\Machine;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LockMachineForTimeSlotsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_mark_a_random_machine_as_locked_between_given_times()
    {
        $machine1 = Machine::factory()->create();

        $now = new CarbonImmutable();

        $response = $this->postJson(route('api.lock.store', [
            'guid' => 'abc1x',
            'from' => $now->format('Y-m-d H:i'),
            'until' => $now->addHour()->format('Y-m-d H:i'),
            'lock_type' => 'any',
            'lock_name' => '',
        ]));

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'id' => $machine1->id,
            ],
        ]);

        tap($machine1->fresh(), function ($machine1) use ($now) {
            $this->assertTrue($machine1->isLocked());
            $this->assertEquals($now->addHour()->format('Y-m-d H:i'), $machine1->locked_until->format('Y-m-d H:i'));
            $this->assertEquals($now->format('Y-m-d H:i'), $machine1->locked_from->format('Y-m-d H:i'));
            $this->assertEquals('abc1x', $machine1->locked_for);
        });
    }

    /** @test */
    public function if_there_arent_any_available_machines_we_get_null_as_the_data_and_a_sensible_status_code()
    {
        $machine1 = Machine::factory()->locked()->create();

        $now = new CarbonImmutable();

        $response = $this->postJson(route('api.lock.store', [
            'guid' => 'abc1x',
            'from' => $now->format('Y-m-d H:i'),
            'until' => $now->addHour()->format('Y-m-d H:i'),
            'lock_type' => 'any',
            'lock_name' => '',
        ]));

        $response->assertStatus(Response::HTTP_CONFLICT);
        $response->assertJson([
            'data' => null
        ]);

        tap($machine1->fresh(), function ($machine1) use ($now) {
            $this->assertTrue($machine1->isLocked());
            $this->assertNull($machine1->locked_until);
            $this->assertNull($machine1->locked_from);
            $this->assertNull($machine1->locked_for);
        });
    }

    /** @test */
    public function we_can_mark_a_machine_in_a_specific_lab_as_locked_between_given_times()
    {
        $machine1 = Machine::factory()->create();
        $lab1 = Lab::factory()->create(['name' => 'Lab 1']);
        $machine2 = Machine::factory()->create();
        $lab2 = Lab::factory()->create(['name' => 'Lab 2']);
        $lab1->members()->save($machine1);
        $lab2->members()->save($machine2);

        $now = new CarbonImmutable();

        $response = $this->postJson(route('api.lock.store', [
            'guid' => 'abc1x',
            'from' => $now->format('Y-m-d H:i'),
            'until' => $now->addHour()->format('Y-m-d H:i'),
            'lock_type' => 'lab',
            'lock_name' => 'Lab 2',
        ]));

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'id' => $machine2->id,
            ],
        ]);

        tap($machine1->fresh(), function ($machine1) use ($now) {
            $this->assertFalse($machine1->isLocked());
            $this->assertNull($machine1->locked_until);
            $this->assertNull($machine1->locked_from);
            $this->assertNull($machine1->locked_for);
        });

        tap($machine2->fresh(), function ($machine1) use ($now) {
            $this->assertTrue($machine1->isLocked());
            $this->assertEquals($now->addHour()->format('Y-m-d H:i'), $machine1->locked_until->format('Y-m-d H:i'));
            $this->assertEquals($now->format('Y-m-d H:i'), $machine1->locked_from->format('Y-m-d H:i'));
            $this->assertEquals('abc1x', $machine1->locked_for);
        });
    }
}
