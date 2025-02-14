<?php

use App\Jobs\LookupDns;
use App\Models\Lab;
use App\Models\LabStat;
use App\Models\Machine;
use Illuminate\Support\Facades\Queue;
use Spatie\TestTime\TestTime;

test('we can record a machine being logged in or out', function () {
    $this->withoutExceptionHandling();
    Queue::fake([LookupDns::class]);
    $response = $this->get(route('api.hello', ['ip' => '1.2.3.4']));

    $response->assertOk();
    expect(Machine::first()->ip)->toEqual('1.2.3.4');
    expect(Machine::first()->logged_in)->toBeTrue();

    $response = $this->get(route('api.goodbye', ['ip' => '1.2.3.4']));

    $response->assertOk();
    expect(Machine::first()->ip)->toEqual('1.2.3.4');
    expect(Machine::first()->logged_in)->toBeFalse();

    // and repeat just to make sure multiple calls work ok
    $response = $this->get(route('api.hello', ['ip' => '1.2.3.4']));

    $response->assertOk();
    expect(Machine::first()->ip)->toEqual('1.2.3.4');
    expect(Machine::first()->logged_in)->toBeTrue();

    $response = $this->get(route('api.goodbye', ['ip' => '1.2.3.4']));

    $response->assertOk();
    expect(Machine::first()->ip)->toEqual('1.2.3.4');
    expect(Machine::first()->logged_in)->toBeFalse();
});

test('when we record a hello we dispatch a job to lookup its dns name', function () {
    $this->withoutExceptionHandling();
    Queue::fake([LookupDns::class]);

    $response = $this->get(route('api.hello', ['ip' => '1.2.3.4']));

    // See DnsLookupTest for coverage of the actual job
    Queue::assertPushed(LookupDns::class);
});

test('we do not dispatch a job to lookup its dns name if it already has been looked up', function () {
    $this->withoutExceptionHandling();
    Queue::fake();
    Machine::factory()->create(['ip' => '1.2.3.4', 'name' => 'blah.example.com']);

    $response = $this->get(route('api.hello', ['ip' => '1.2.3.4']));

    Queue::assertNotPushed(LookupDns::class);
});

test('if we try and say goodbye for an ip that isnt in the database we get a 404', function () {
    $response = $this->get(route('api.goodbye', ['ip' => '1.2.3.4']));

    $response->assertStatus(404);
});

test('we can record extra json data about a machine', function () {
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
        expect($machine->ip)->toEqual('1.2.3.4');
        expect($machine->logged_in)->toBeFalse();
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
});

test('if an ip isnt supplied we make a best guess at the ip', function () {
    $this->withoutExceptionHandling();
    $response = $this->get(route('api.hello'));

    $response->assertOk();
    expect(Machine::first()->ip)->toEqual('127.0.0.1');
    expect(Machine::first()->logged_in)->toBeTrue();

    $response = $this->get(route('api.goodbye'));

    $response->assertOk();
    expect(Machine::first()->ip)->toEqual('127.0.0.1');
    expect(Machine::first()->logged_in)->toBeFalse();
});

test('when a machine logs in or out we store its ip and user agent and current timestamp', function () {
    $this->withoutExceptionHandling();

    $response = $this->get(route('api.hello'));

    $response->assertOk();
    tap(Machine::first(), function ($machine) {
        expect($machine->user_agent)->toEqual('Symfony');
        expect($machine->created_at->format('Y-m-d H:i'))->toEqual(now()->format('Y-m-d H:i'));
        expect($machine->logged_in)->toBeTrue();
    });
});

test('the machines log can be automatically trimmed by a scheduled task', function () {
    config(['labmon.machine_log_days' => 3]);
    $currentLog = Machine::factory()->create();
    $oldLog = Machine::factory()->create(['updated_at' => now()->subDays(4)]);
    expect(Machine::count())->toEqual(2);

    $this->artisan('labmon:trimlogs');

    expect(Machine::count())->toEqual(1);
    expect(Machine::first()->is($currentLog))->toBeTrue();
});

test('we can get the last time an ip was seen', function () {
    $response = $this->get(route('api.hello'));
    $response = $this->get(route('api.hello', ['ip' => '1.2.3.4']));

    $response = $this->get(route('api.lastseen', ['ip' => '1.2.3.4']));

    $response->assertOk();
    $response->assertJson([
        'data' => Machine::where('ip', '=', '1.2.3.4')->first()->toArray(),
    ]);
});

test('we can get the busyness of a lab', function () {
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
});

test('we can get the stats for all labs between given dates', function () {
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
});

test('we can get a list of machines availabe for remote desktop on an always on lab', function () {
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
});

test('a lab that is not available at all for rdp returns no results', function () {
    $this->withoutExceptionHandling();
    $lab = Lab::factory()->create(['always_remote_access' => false, 'limited_remote_access' => false]);
    $machines = Machine::factory()->count(10)->create(['lab_id' => $lab->id]);

    $response = $this->getJson(route('api.lab.rdp_machines', $lab->name));

    $response->assertOk();
    $response->assertJsonCount(0, 'data');
});

test('a lab that is limited available for rdp returns results if the time is right', function () {
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
});

test('a lab that is limited available for rdp doesnt returns correct results based on the date and time', function () {
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
});

test('we can get a list of labs available for rdp', function () {
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
});

test('we can get a list of all machines available for rdp', function () {
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
});

test('we can get the list of labs available for the lab usage stats', function () {
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
});

test('we can get all the stats for the lab usage stats in one api call', function () {
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
});

test('we can get a list of all machines', function () {
    $machines = Machine::factory()->count(5)->create();

    $response = $this->getJson(route('api.machine.index'));

    $response->assertOk();
    $machines->each(function ($machine) use ($response) {
        $response->assertJsonFragment([
            'data' => Machine::orderBy('ip')->get()->toArray(),
        ]);
    });
});
