<?php

namespace Tests\Feature;

use App\LabMachine;
use App\MachineLog;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_record_a_machine_being_logged_in_or_out()
    {
        $this->withoutExceptionHandling();
        $response = $this->get(route('api.hello', ['ip' => '1.2.3.4']));

        $response->assertOk();
        $this->assertEquals('1.2.3.4', MachineLog::first()->ip);
        $this->assertTrue(MachineLog::first()->logged_in);
        $this->assertDatabaseHas('lab_machines', ['ip' => '1.2.3.4']);

        $response = $this->get(route('api.goodbye', ['ip' => '1.2.3.4']));

        $response->assertOk();
        $this->assertEquals('1.2.3.4', MachineLog::all()->last()->ip);
        $this->assertFalse(MachineLog::all()->last()->logged_in);
        $this->assertDatabaseMissing('lab_machines', ['ip' => '1.2.3.4']);
    }

    /** @test */
    public function if_an_ip_isnt_supplied_we_make_a_best_guess_at_the_ip()
    {
        $this->withoutExceptionHandling();
        $response = $this->get(route('api.hello'));

        $response->assertOk();
        $this->assertEquals('127.0.0.1', MachineLog::first()->ip);
        $this->assertTrue(MachineLog::first()->logged_in);
        $this->assertDatabaseHas('lab_machines', ['ip' => '127.0.0.1']);

        $response = $this->get(route('api.goodbye'));

        $response->assertOk();
        $this->assertEquals('127.0.0.1', MachineLog::all()->last()->ip);
        $this->assertFalse(MachineLog::all()->last()->logged_in);
        $this->assertDatabaseMissing('lab_machines', ['ip' => '127.0.0.1']);
    }

    /** @test */
    public function when_a_machine_logs_in_or_out_we_store_its_ip_and_user_agent_and_current_timestamp()
    {
        $this->withoutExceptionHandling();

        $response = $this->get(route('api.hello'));

        $response->assertOk();
        tap(MachineLog::first(), function ($log) {
            $this->assertEquals('Symfony', $log->user_agent);
            $this->assertEquals(now()->format('Y-m-d H:i'), $log->created_at->format('Y-m-d H:i'));
            $this->assertTrue($log->logged_in);
        });

        $response = $this->get(route('api.goodbye'));

        $response->assertOk();
        tap(MachineLog::all()->last(), function ($log) {
            $this->assertEquals('Symfony', $log->user_agent);
            $this->assertEquals(now()->format('Y-m-d H:i'), $log->created_at->format('Y-m-d H:i'));
            $this->assertFalse($log->logged_in);
        });
    }

    /** @test */
    public function the_machine_log_can_be_automatically_trimmed_by_a_scheduled_task()
    {
        config(['labmon.machine_log_days' => 3]);
        $currentLog = factory(MachineLog::class)->create();
        $oldLog = factory(MachineLog::class)->create(['created_at' => now()->subDays(4)]);
        $this->assertEquals(2, MachineLog::count());

        $this->artisan('labmon:trimlogs');

        $this->assertEquals(1, MachineLog::count());
        $this->assertTrue(MachineLog::first()->is($currentLog));
    }

    /** @test */
    public function we_can_get_the_last_time_an_ip_was_seen()
    {
        $response = $this->get(route('api.hello'));

        $response = $this->get(route('api.lastseen', ['ip' => '127.0.0.1']));

        $response->assertOk();
        $response->assertJson([
            'data' => MachineLog::first()->toArray(),
        ]);
    }
}
