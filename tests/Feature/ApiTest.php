<?php

namespace Tests\Feature;

use App\Jobs\LookupDns;
use App\Models\Lab;
use App\Models\LabStat;
use App\Models\Machine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_record_a_machine_being_logged_in_or_out(): void
    {
        $this->withoutExceptionHandling();
        Queue::fake();
        info('1'.microtime(true));
        $response = $this->get(route('api.hello', ['ip' => '1.2.3.4']));
        info('2'.microtime(true));

        $response->assertOk();
        $this->assertEquals('1.2.3.4', Machine::first()->ip);
        info('3'.microtime(true));
        $this->assertTrue(Machine::first()->logged_in);

        info('4'.microtime(true));
        $response = $this->get(route('api.goodbye', ['ip' => '1.2.3.4']));
        info('5'.microtime(true));

        $response->assertOk();
        $this->assertEquals('1.2.3.4', Machine::first()->ip);
        $this->assertFalse(Machine::first()->logged_in);

        info('6'.microtime(true));
        // and repeat just to make sure multiple calls work ok
        $response = $this->get(route('api.hello', ['ip' => '1.2.3.4']));

        info('7'.microtime(true));
        $response->assertOk();
        $this->assertEquals('1.2.3.4', Machine::first()->ip);
        $this->assertTrue(Machine::first()->logged_in);
        info('8'.microtime(true));

        $response = $this->get(route('api.goodbye', ['ip' => '1.2.3.4']));

        info('9'.microtime(true));

        $response->assertOk();
        $this->assertEquals('1.2.3.4', Machine::first()->ip);
        $this->assertFalse(Machine::first()->logged_in);
    }

    /** @test */
    public function when_we_record_a_hello_we_dispatch_a_job_to_lookup_its_dns_name(): void
    {
        $this->withoutExceptionHandling();
        Queue::fake();

        $response = $this->get(route('api.hello', ['ip' => '1.2.3.4']));

        // See DnsLookupTest for coverage of the actual job
        Queue::assertPushed(LookupDns::class);
    }

    /** @test */
    public function we_do_not_dispatch_a_job_to_lookup_its_dns_name_if_it_already_has_been_looked_up(): void
    {
        $this->withoutExceptionHandling();
        Queue::fake();
        Machine::factory()->create(['ip' => '1.2.3.4', 'name' => 'blah.example.com']);

        $response = $this->get(route('api.hello', ['ip' => '1.2.3.4']));

        Queue::assertNotPushed(LookupDns::class);
    }

    /** @test */
    public function if_we_try_and_say_goodbye_for_an_ip_that_isnt_in_the_database_we_get_a_404(): void
    {
        $response = $this->get(route('api.goodbye', ['ip' => '1.2.3.4']));

        $response->assertStatus(404);
    }

    /** @test */
    public function we_can_record_extra_json_data_about_a_machine(): void
    {
        $this->withoutExceptionHandling();
        $response = $this->postJson(route('api.machine.update', ['ip' => '1.2.3.4']), [
            'meta' => [
                'spec' => [
                    'model' => 'blah',
                    'ram' => 'loads',
                ],
                'users' => [
                    'fred', 'ginger',
                ],
            ],
        ]);

        $response->assertOk();
        tap(Machine::first(), function ($machine) {
            $this->assertEquals('1.2.3.4', $machine->ip);
            $this->assertFalse($machine->logged_in);
            $this->assertEquals([
                'spec' => [
                    'model' => 'blah',
                    'ram' => 'loads',
                ],
                'users' => [
                    'fred', 'ginger',
                ],
            ], $machine->meta);
        });
    }

    /** @test */
    public function if_an_ip_isnt_supplied_we_make_a_best_guess_at_the_ip(): void
    {
        $this->withoutExceptionHandling();
        $response = $this->get(route('api.hello'));

        $response->assertOk();
        $this->assertEquals('127.0.0.1', Machine::first()->ip);
        $this->assertTrue(Machine::first()->logged_in);

        $response = $this->get(route('api.goodbye'));

        $response->assertOk();
        $this->assertEquals('127.0.0.1', Machine::first()->ip);
        $this->assertFalse(Machine::first()->logged_in);
    }

    /** @test */
    public function when_a_machine_logs_in_or_out_we_store_its_ip_and_user_agent_and_current_timestamp(): void
    {
        $this->withoutExceptionHandling();

        $response = $this->get(route('api.hello'));

        $response->assertOk();
        tap(Machine::first(), function ($machine) {
            $this->assertEquals('Symfony', $machine->user_agent);
            $this->assertEquals(now()->format('Y-m-d H:i'), $machine->created_at->format('Y-m-d H:i'));
            $this->assertTrue($machine->logged_in);
        });
    }

    /** @test */
    public function the_machines_log_can_be_automatically_trimmed_by_a_scheduled_task(): void
    {
        config(['labmon.machine_log_days' => 3]);
        $currentLog = Machine::factory()->create();
        $oldLog = Machine::factory()->create(['updated_at' => now()->subDays(4)]);
        $this->assertEquals(2, Machine::count());

        $this->artisan('labmon:trimlogs');

        $this->assertEquals(1, Machine::count());
        $this->assertTrue(Machine::first()->is($currentLog));
    }

    /** @test */
    public function we_can_get_the_last_time_an_ip_was_seen(): void
    {
        $response = $this->get(route('api.hello'));
        $response = $this->get(route('api.hello', ['ip' => '1.2.3.4']));

        $response = $this->get(route('api.lastseen', ['ip' => '1.2.3.4']));

        $response->assertOk();
        $response->assertJson([
            'data' => Machine::where('ip', '=', '1.2.3.4')->first()->toArray(),
        ]);
    }

    /** @test */
    public function we_can_get_the_busyness_of_a_lab(): void
    {
        $this->withoutExceptionHandling();
        $lab = Lab::factory()->create();
        $machine1 = Machine::factory()->create(['lab_id' => $lab->id, 'logged_in' => true]);
        $machine2 = Machine::factory()->create(['lab_id' => $lab->id, 'logged_in' => false]);
        $machine3 = Machine::factory()->create(['lab_id' => $lab->id, 'logged_in' => true]);
        $machine4 = Machine::factory()->create(['lab_id' => null, 'logged_in' => true]);

        $response = $this->getJson(route('api.lab.busy', $lab->name));

        $response->assertOk();
        $response->assertJson([
            'data' => [
                'machines_total' => 3,
                'logged_in_total' => 2,
                'logged_in_percent' => '66.67',
            ],
        ]);
    }

    /** @test */
    public function we_can_get_the_stats_for_all_labs_between_given_dates(): void
    {
        $stat1 = LabStat::factory()->create(['created_at' => now()->subDays(200)]);
        $stat2 = LabStat::factory()->create(['created_at' => now()->subDays(100)]);
        $stat3 = LabStat::factory()->create(['created_at' => now()->subDays(1)]);

        $response = $this->getJson(
            route(
                'api.labstats.dates',
                [
                    'from' => now()->subDays(101)->format('Y-m-d'),
                    'until' => now()->format('Y-m-d'),
                ]
            )
        );

        $response->assertOk();
        $response->assertJson([
            'data' => [
                [
                    'id' => $stat2->id,
                    'machine_total' => $stat2->machine_total,
                    'logged_in_total' => $stat2->logged_in_total,
                ],
                [
                    'id' => $stat3->id,
                    'machine_total' => $stat3->machine_total,
                    'logged_in_total' => $stat3->logged_in_total,
                ],
            ],
        ]);
    }

    /** @test */
    public function we_can_get_a_list_of_machines_availabe_for_remote_desktop_on_an_always_on_lab(): void
    {
        $this->withoutExceptionHandling();
        $lab = Lab::factory()->create(['always_remote_access' => true]);
        $inUseMachines = Machine::factory()->count(5)->create(['lab_id' => $lab->id, 'logged_in' => true]);
        $notInUseMachines = Machine::factory()->count(3)->create(['lab_id' => $lab->id, 'logged_in' => false]);

        $response = $this->getJson(route('api.lab.rdp_machines', $lab->name));

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
        // machines are returned in random order, so just assume the data is correct for now
        // until I figure out how to get the json data to compare with something like ->contains()
        // $response->assertJson([
        //     'data' =>[
        //         [
        //             'id' => $notInUseMachines[0]->id,
        //         ]
        //     ]
        // ]);
    }

    /** @test */
    public function a_lab_that_is_not_available_at_all_for_rdp_returns_no_results(): void
    {
        $this->withoutExceptionHandling();
        $lab = Lab::factory()->create(['always_remote_access' => false, 'limited_remote_access' => false]);
        $machines = Machine::factory()->count(10)->create(['lab_id' => $lab->id]);

        $response = $this->getJson(route('api.lab.rdp_machines', $lab->name));

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    /** @test */
    public function a_lab_that_is_limited_available_for_rdp_returns_results_if_the_time_is_right(): void
    {
        $this->withoutExceptionHandling();
        $lab = Lab::factory()->create(['always_remote_access' => false, 'limited_remote_access' => true]);
        $inUseMachines = Machine::factory()->count(5)->create(['lab_id' => $lab->id, 'logged_in' => true]);
        $notInUseMachines = Machine::factory()->count(3)->create(['lab_id' => $lab->id, 'logged_in' => false]);
        option(['remote-start-hour' => 18]);
        option(['remote-end-hour' => 8]);

        option(['remote-start-summer' => '01/Jun']);
        option(['remote-end-summer' => '31/Aug']);

        TestTime::freeze('Y-m-d H:i', '2019-06-12 20:00');

        $response = $this->getJson(route('api.lab.rdp_machines', $lab->name));

        $response->assertOk();
        $response->assertJsonCount(3, 'data');

        option(['remote-start-xmas' => '01/Dec']);
        option(['remote-end-xmas' => '31/Dec']);

        TestTime::freeze('Y-m-d H:i', '2019-12-20 20:00');

        $response = $this->getJson(route('api.lab.rdp_machines', $lab->name));

        $response->assertOk();
        $response->assertJsonCount(3, 'data');

        option(['remote-start-easter' => '01/Apr']);
        option(['remote-end-easter' => '31/Apr']);

        TestTime::freeze('Y-m-d H:i', '2019-04-20 20:00');

        $response = $this->getJson(route('api.lab.rdp_machines', $lab->name));

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function a_lab_that_is_limited_available_for_rdp_doesnt_returns_correct_results_based_on_the_date_and_time(): void
    {
        $this->withoutExceptionHandling();
        $lab = Lab::factory()->create(['always_remote_access' => false, 'limited_remote_access' => true]);
        $inUseMachines = Machine::factory()->count(5)->create(['lab_id' => $lab->id, 'logged_in' => true]);
        $notInUseMachines = Machine::factory()->count(3)->create(['lab_id' => $lab->id, 'logged_in' => false]);
        option(['remote-start-hour' => 18]);
        option(['remote-end-hour' => 8]);
        option(['remote-start-summer' => '01/Jun']);
        option(['remote-end-summer' => '31/Aug']);
        option(['remote-start-xmas' => '01/Dec']);
        option(['remote-end-xmas' => '31/Dec']);
        option(['remote-start-easter' => '01/Apr']);
        option(['remote-end-easter' => '31/Apr']);

        // during the day, outside of holiday - no rdp available
        TestTime::freeze('Y-m-d H:i', '2019-05-12 14:00');

        $response = $this->getJson(route('api.lab.rdp_machines', $lab->name));

        $response->assertOk();
        $response->assertJsonCount(0, 'data');

        // out-of-hours time, outside of holiday - rdp available
        TestTime::freeze('Y-m-d H:i', '2019-02-12 20:00');

        $response = $this->getJson(route('api.lab.rdp_machines', $lab->name));

        $response->assertOk();
        $response->assertJsonCount(3, 'data');

        // during the day, during a holiday - rdp available
        TestTime::freeze('Y-m-d H:i', '2019-20-12 14:00');

        $response = $this->getJson(route('api.lab.rdp_machines', $lab->name));

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function we_can_get_a_list_of_labs_available_for_rdp(): void
    {
        $this->withoutExceptionHandling();
        $limitedLab = Lab::factory()->create(['always_remote_access' => false, 'limited_remote_access' => true]);
        $unlimitedLab = Lab::factory()->create(['always_remote_access' => true, 'limited_remote_access' => false]);
        $offLimitsLab = Lab::factory()->create(['always_remote_access' => false, 'limited_remote_access' => false]);
        option(['remote-start-hour' => 18]);
        option(['remote-end-hour' => 8]);
        option(['remote-start-summer' => '01/Jun']);
        option(['remote-end-summer' => '31/Aug']);
        option(['remote-start-xmas' => '01/Dec']);
        option(['remote-end-xmas' => '31/Dec']);
        option(['remote-start-easter' => '01/Apr']);
        option(['remote-end-easter' => '31/Apr']);

        // during the day, outside of holiday - only unlimited lab available
        TestTime::freeze('Y-m-d H:i', '2019-05-12 14:00');

        $response = $this->getJson(route('api.lab.rdp_labs'));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJson([
            'data' => [
                [
                    'id' => $unlimitedLab->id,
                ],
            ],
        ]);

        // evening, outside of holiday - unlimited and limited available
        TestTime::freeze('Y-m-d H:i', '2019-05-12 20:00');

        $response = $this->getJson(route('api.lab.rdp_labs'));

        $response->assertOk();
        $response->assertJsonCount(2, 'data');

        // daytime, during a holiday - unlimited and limited available
        TestTime::freeze('Y-m-d H:i', '2019-12-12 14:00');

        $response = $this->getJson(route('api.lab.rdp_labs'));

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    /** @test */
    public function we_can_get_a_list_of_all_machines_available_for_rdp(): void
    {
        $this->withoutExceptionHandling();
        $limitedLab = Lab::factory()->create(['always_remote_access' => false, 'limited_remote_access' => true]);
        $unlimitedLab = Lab::factory()->create(['always_remote_access' => true, 'limited_remote_access' => false]);
        $offLimitsLab = Lab::factory()->create(['always_remote_access' => false, 'limited_remote_access' => false]);
        Machine::factory()->count(3)->create(['lab_id' => $limitedLab->id, 'logged_in' => false]);
        Machine::factory()->count(4)->create(['lab_id' => $unlimitedLab->id, 'logged_in' => false]);
        Machine::factory()->count(2)->create(['lab_id' => $offLimitsLab->id, 'logged_in' => false]);
        Machine::factory()->create(['lab_id' => $unlimitedLab->id, 'logged_in' => false, 'is_locked' => true]);
        option(['remote-start-hour' => 18]);
        option(['remote-end-hour' => 8]);
        option(['remote-start-summer' => '01/Jun']);
        option(['remote-end-summer' => '31/Aug']);
        option(['remote-start-xmas' => '01/Dec']);
        option(['remote-end-xmas' => '31/Dec']);
        option(['remote-start-easter' => '01/Apr']);
        option(['remote-end-easter' => '31/Apr']);

        // evening, outside of holiday - unlimited and limited available
        TestTime::freeze('Y-m-d H:i', '2019-05-12 20:00');

        $response = $this->getJson(route('api.machines.rdp'));

        $response->assertOk();
        // there are 3 machines in the limited lab, but it's the evening so it shows up
        // 5 machines in the unlimited lab, but one of them is marked as locked, so shouldn't show up
        // giving 7 available
        $response->assertJsonCount(7, 'data');
    }

    /** @test */
    public function we_can_get_the_list_of_labs_available_for_the_lab_usage_stats(): void
    {
        $lab1 = Lab::factory()->create(['is_on_graphs' => true]);
        $lab2 = Lab::factory()->create(['is_on_graphs' => false]);
        $lab3 = Lab::factory()->create(['is_on_graphs' => true]);

        $response = $this->getJson(route('api.lab.graphable'));

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJson([
            'data' => [
                [
                    'id' => $lab1->id,
                    'name' => $lab1->name,
                ],
                [
                    'id' => $lab3->id,
                    'name' => $lab3->name,
                ],
            ],
        ]);
    }

    /** @test */
    public function we_can_get_all_the_stats_for_the_lab_usage_stats_in_one_api_call(): void
    {
        $this->withoutExceptionHandling();
        $lab1 = Lab::factory()->create(['is_on_graphs' => true, 'name' => 'ABC1']);
        $lab2 = Lab::factory()->create(['is_on_graphs' => false, 'name' => 'DEF1']);
        $lab3 = Lab::factory()->create(['is_on_graphs' => true, 'name' => 'GHK1']);
        $inUseMachines = Machine::factory()->count(5)->create(['lab_id' => $lab1->id, 'logged_in' => true]);
        $notInUseMachines = Machine::factory()->count(3)->create(['lab_id' => $lab1->id, 'logged_in' => false]);
        $inUseMachines = Machine::factory()->count(3)->create(['lab_id' => $lab3->id, 'logged_in' => true]);
        $notInUseMachines = Machine::factory()->count(5)->create(['lab_id' => $lab3->id, 'logged_in' => false]);

        $response = $this->getJson(route('api.lab.graph_stats'));

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJson([
            'data' => [
                [
                    'id' => $lab1->id,
                    'name' => $lab1->name,
                    'stats' => [
                        'machine_total' => $lab1->members()->count(),
                        'logged_in_total' => $lab1->members()->online()->count(),
                    ],
                ],
                [
                    'id' => $lab3->id,
                    'name' => $lab3->name,
                    'stats' => [
                        'machine_total' => $lab3->members()->count(),
                        'logged_in_total' => $lab3->members()->online()->count(),
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function we_can_get_a_list_of_all_machines(): void
    {
        $machines = Machine::factory()->count(5)->create();

        $response = $this->getJson(route('api.machine.index'));

        $response->assertOk();
        $machines->each(function ($machine) use ($response) {
            $response->assertJsonFragment([
                'data' => Machine::orderBy('ip')->get()->toArray(),
            ]);
        });
    }
}
