<?php

use App\Jobs\LookupDns;
use App\Models\Machine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use TitasGailius\Terminal\Terminal;

uses(TestCase::class);
uses(RefreshDatabase::class);

test('a machine can lookup the dns name for its ip address', function () {
    $machine = Machine::factory()->create(['ip' => '1.1.1.1', 'name' => null]);

    $machine->lookupDns();

    expect($machine->fresh()->name)->toEqual('one.one.one.one');
});

test('if a custom dns resolver is configured then it is preferred over phps internal calls', function () {
    $machine = Machine::factory()->create(['ip' => '1.1.1.1', 'name' => null]);
    config(['labmon.dns_server' => '1.1.1.1']);

    Terminal::fake([
        'host 1.1.1.1 1.1.1.1' => "some-guff\n1.1.1.1.in-addr.arpa domain name pointer my.fake.domain.",
    ]);

    $machine->lookupDns();

    expect($machine->fresh()->name)->toEqual('my.fake.domain');
});

test('there is an artisan command to lookup all machine dns names', function () {
    $machine1 = Machine::factory()->create(['ip' => '1.1.1.1']);
    $machine2 = Machine::factory()->create(['ip' => '8.8.8.8']);

    $this->artisan('labmon:refreshdns');

    expect($machine1->fresh()->name)->toEqual('one.one.one.one');
    expect($machine2->fresh()->name)->toEqual('dns.google');
});

test('the artisan command is registered with the schedular', function () {
    $this->assertCommandIsScheduled('labmon:refreshdns');
});

test('the lookup dns job can lookup the dns for a machine', function () {
    $machine = Machine::factory()->create([
        'ip' => '1.1.1.1',
        'name' => null,
    ]);

    LookupDns::dispatch($machine);

    expect($machine->fresh()->name)->toEqual('one.one.one.one');
});
