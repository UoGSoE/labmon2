<?php

namespace Tests\Feature;

use App\Jobs\LookupDns;
use App\Machine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class DnsLookupTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_machine_can_lookup_the_dns_name_for_its_ip_address()
    {
        $machine = factory(Machine::class)->create(['ip' => '1.1.1.1', 'name' => null]);

        $machine->lookupDns();

        $this->assertEquals('one.one.one.one', $machine->fresh()->name);
    }

    /** @test */
    public function if_a_custom_dns_resolver_is_configured_then_it_is_preferred_over_phps_internal_calls()
    {
        $this->markTestSkipped('TODO');
        return;
        $machine = factory(Machine::class)->create(['ip' => '1.1.1.1', 'name' => null]);
        config(['labmon.dns_server' => '1.1.1.1']);

        $this->mock(Process::class, function ($mock) {
            $mock->shouldReceive('run')
                ->once()
                ->andReturn('one.one.one.one');
        });

        $machine->lookupDns();

        $this->assertEquals('one.one.one.one', $machine->fresh()->name);
    }

    /** @test */
    public function there_is_an_artisan_command_to_lookup_all_machine_dns_names()
    {
        $machine1 = factory(Machine::class)->create(['ip' => '1.1.1.1']);
        $machine2 = factory(Machine::class)->create(['ip' => '8.8.8.8']);

        $this->artisan('labmon:refreshdns');

        $this->assertEquals('one.one.one.one', $machine1->fresh()->name);
        $this->assertEquals('dns.google', $machine2->fresh()->name);
    }

    /** @test */
    public function the_artisan_command_is_registered_with_the_schedular()
    {
        $this->assertCommandIsScheduled('labmon:refreshdns');
    }

    /** @test */
    public function the_lookup_dns_job_can_lookup_the_dns_for_a_machine()
    {
        $machine = factory(Machine::class)->create([
            'ip' => '1.1.1.1',
            'name' => null,
        ]);

        LookupDns::dispatch($machine);

        $this->assertEquals('one.one.one.one', $machine->fresh()->name);
    }
}
