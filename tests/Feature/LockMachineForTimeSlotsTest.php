<?php

namespace Tests\Feature;

use App\Models\Lab;
use Tests\TestCase;
use App\Models\Machine;
use Carbon\CarbonImmutable;
use App\Models\MachineBooking;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LockMachineForTimeSlotsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        config(['labmon.api_key' => 'verysecretkey']);
    }

    /** @test */
    public function calls_to_lock_a_machine_with_the_wrong_or_missing_api_key_header_are_rejected()
    {
        // see the setUp method for the config() call to set the app api key
        $machine1 = Machine::factory()->create();

        $now = new CarbonImmutable();

        $response = $this->postJson(route('api.lock.store', [
            'guid' => 'abc1x',
            'from' => $now->format('Y-m-d H:i'),
            'until' => $now->addHour()->format('Y-m-d H:i'),
            'lock_type' => 'any',
            'lock_name' => '',
        ]), [], [
            'X-API-KEY' => 'thewrongkey',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $response = $this->postJson(route('api.lock.store', [
            'guid' => 'abc1x',
            'from' => $now->format('Y-m-d H:i'),
            'until' => $now->addHour()->format('Y-m-d H:i'),
            'lock_type' => 'any',
            'lock_name' => '',
        ]), [], []);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }


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
        ]), [], [
            'X-API-KEY' => 'verysecretkey',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'id' => $machine1->id,
            ],
        ]);

        tap($machine1->fresh(), function ($machine1) use ($now) {
            $this->assertEquals($now->addHour()->format('Y-m-d H:i'), $machine1->bookings->first()->end->format('Y-m-d H:i'));
            $this->assertEquals($now->format('Y-m-d H:i'), $machine1->bookings->first()->start->format('Y-m-d H:i'));
            $this->assertEquals('abc1x', $machine1->bookings->first()->guid);
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
        ]), [], [
            'X-API-KEY' => 'verysecretkey',
        ]);

        $response->assertStatus(Response::HTTP_CONFLICT);
        $response->assertJson([
            'data' => null
        ]);

        tap($machine1->fresh(), function ($machine1) use ($now) {
            $this->assertCount(0, $machine1->bookings);
            $this->assertTrue($machine1->isLocked());
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
        ]), [], [
            'X-API-KEY' => 'verysecretkey',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'id' => $machine2->id,
            ],
        ]);

        tap($machine1->fresh(), function ($machine1) use ($now) {
            $this->assertFalse($machine1->isLocked());
            $this->assertCount(0, $machine1->bookings);
        });

        tap($machine2->fresh(), function ($machine2) use ($now) {
            $this->assertFalse($machine2->isLocked());
            $this->assertEquals($now->addHour()->format('Y-m-d H:i'), $machine2->bookings->first()->end->format('Y-m-d H:i'));
            $this->assertEquals($now->format('Y-m-d H:i'), $machine2->bookings->first()->start->format('Y-m-d H:i'));
            $this->assertEquals('abc1x', $machine2->bookings->first()->guid);
        });
    }

    /** @test */
    public function we_can_mark_a_machine_in_a_specific_building_as_locked_between_given_times()
    {
        $machine1 = Machine::factory()->create();
        $lab1 = Lab::factory()->create(['name' => 'JWS Lab 1']);
        $machine2 = Machine::factory()->create();
        $lab2 = Lab::factory()->create(['name' => 'RankLab 2']);
        $lab1->members()->save($machine1);
        $lab2->members()->save($machine2);

        $now = new CarbonImmutable();

        $response = $this->postJson(route('api.lock.store', [
            'guid' => 'abc1x',
            'from' => $now->format('Y-m-d H:i'),
            'until' => $now->addHour()->format('Y-m-d H:i'),
            'lock_type' => 'building',
            'lock_name' => 'JWS',
        ]), [], [
            'X-API-KEY' => 'verysecretkey',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'id' => $machine1->id,
            ],
        ]);

        tap($machine1->fresh(), function ($machine1) use ($now) {
            $this->assertFalse($machine1->isLocked());
            $this->assertEquals(now()->addHour()->format('Y-m-d H:i'), $machine1->bookings->first()->end->format('Y-m-d H:i'));
            $this->assertEquals(now()->format('Y-m-d H:i'), $machine1->bookings->first()->start->format('Y-m-d H:i'));
            $this->assertEquals('abc1x', $machine1->bookings->first()->guid);
        });

        tap($machine2->fresh(), function ($machine2) use ($now) {
            $this->assertFalse($machine2->isLocked());
            $this->assertCount(0, $machine2->bookings);
        });
    }

    /** @test */
    public function we_can_mark_a_machine_in_a_specific_school_as_locked_between_given_times()
    {
        $machine1 = Machine::factory()->create();
        $lab1 = Lab::factory()->create(['name' => 'JWS Lab 1', 'school' => 'School 1']);
        $machine2 = Machine::factory()->create();
        $lab2 = Lab::factory()->create(['name' => 'RankLab 2', 'school' => 'School 2']);
        $lab1->members()->save($machine1);
        $lab2->members()->save($machine2);

        $now = new CarbonImmutable();

        $response = $this->postJson(route('api.lock.store', [
            'guid' => 'abc1x',
            'from' => $now->format('Y-m-d H:i'),
            'until' => $now->addHour()->format('Y-m-d H:i'),
            'lock_type' => 'school',
            'lock_name' => 'School 2',
        ]), [], [
            'X-API-KEY' => 'verysecretkey',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'id' => $machine2->id,
            ],
        ]);

        tap($machine1->fresh(), function ($machine1) use ($now) {
            $this->assertCount(0, $machine1->bookings);
        });

        tap($machine2->fresh(), function ($machine2) use ($now) {
            $this->assertEquals(now()->addHour()->format('Y-m-d H:i'), $machine2->bookings->first()->end->format('Y-m-d H:i'));
            $this->assertEquals(now()->format('Y-m-d H:i'), $machine2->bookings->first()->start->format('Y-m-d H:i'));
            $this->assertEquals('abc1x', $machine2->bookings->first()->guid);
        });
    }

    /** @test */
    public function there_is_a_scheduled_task_to_lock_and_unlock_machines_during_the_correct_times()
    {
        $this->assertCommandIsScheduled('labmon:update-locks');
    }

    /** @test */
    public function the_scheduled_command_does_lock_and_unlock_machines_at_the_correct_times()
    {
        $machineUnlockedButNowBooked = Machine::factory()->create(['is_locked' => false]);
        $booking1 = $machineUnlockedButNowBooked->bookings()->create([
            'guid' => 'abc1x',
            'start' => now(),
            'end' => now()->addHour(),
        ]);
        $machineUnlockedButBookedInTheFuture = Machine::factory()->create(['is_locked' => false]);
        $booking2 = $machineUnlockedButBookedInTheFuture->bookings()->create([
            'guid' => 'abc2x',
            'start' => now()->addHours(1),
            'end' => now()->addHours(2),
        ]);
        $machineLockedButBookingFinished = Machine::factory()->create(['is_locked' => true]);
        $booking3 = $machineLockedButBookingFinished->bookings()->create([
            'guid' => 'abc3x',
            'start' => now()->subHours(2),
            'end' => now()->subHours(1),
        ]);
        $machineUnlockedButCurrentlyBooked = Machine::factory()->create(['is_locked' => false]);
        $booking4 = $machineUnlockedButCurrentlyBooked->bookings()->create([
            'guid' => 'abc4x',
            'start' => now()->subHours(1),
            'end' => now()->addHours(1),
        ]);

        $this->artisan('labmon:update-locks');

        $this->assertTrue($machineUnlockedButNowBooked->fresh()->isLocked());
        $this->assertFalse($machineUnlockedButBookedInTheFuture->fresh()->isLocked());
        $this->assertFalse($machineLockedButBookingFinished->fresh()->isLocked());
        $this->assertTrue($machineUnlockedButCurrentlyBooked->fresh()->isLocked());
    }
}
