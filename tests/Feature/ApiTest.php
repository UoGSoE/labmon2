<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.redis.default.database' => 1]);
        Redis::del('labmachines');
        Redis::del('machinelog');
    }

    /** @test */
    public function we_can_record_a_machine_being_logged_in_or_out()
    {
        $response = $this->get(route('api.hello', ['ip' => '1.2.3.4']));

        $response->assertOk();
        $this->assertTrue(collect(Redis::smembers('labmachines'))->contains('1.2.3.4'));

        $response = $this->get(route('api.goodbye', ['ip' => '1.2.3.4']));

        $response->assertOk();
        $this->assertFalse(collect(Redis::smembers('labmachines'))->contains('1.2.3.4'));
    }

    /** @test */
    public function if_an_ip_isnt_supplied_we_make_a_best_guess_at_the_ip()
    {
        $this->withoutExceptionHandling();
        $response = $this->get(route('api.hello'));

        $response->assertOk();
        $this->assertTrue(collect(Redis::smembers('labmachines'))->contains('127.0.0.1'));

        $response = $this->get(route('api.goodbye'));

        $response->assertOk();
        $this->assertFalse(collect(Redis::smembers('labmachines'))->contains('127.0.0.1'));
    }

    /** @test */
    public function when_a_machine_logs_in_or_out_we_store_its_ip_and_user_agent_and_current_timestamp()
    {
        $this->withoutExceptionHandling();

        $response = $this->get(route('api.hello'));

        $response->assertOk();
        $now = now()->timestamp;
        $userAgent = 'Symfony';
        $this->assertEquals("127.0.0.1:{$now}:hello:{$userAgent}", Redis::lindex('machinelog', 0));

        $response = $this->get(route('api.goodbye'));

        $response->assertOk();
        $now = now()->timestamp;
        $userAgent = 'Symfony';
        $this->assertEquals("127.0.0.1:{$now}:goodbye:{$userAgent}", Redis::lindex('machinelog', 0));
    }

    /** @test */
    public function we_limit_the_amount_of_data_in_the_machinelog()
    {
        config(['labmon.max_machine_logs' => 3]);

        $response = $this->get(route('api.hello'));
        $response = $this->get(route('api.goodbye'));
        $response = $this->get(route('api.hello'));
        $response = $this->get(route('api.goodbye'));
        $response = $this->get(route('api.hello'));
        $response = $this->get(route('api.goodbye'));

        // we count 4 as the limit is zero-based - so '3' means 0-3
        $this->assertCount(4, Redis::lrange('machinelog', 0, -1));
    }

    /** @test */
    public function we_record_the_last_seen_timestamp_for_each_machine()
    {
        $response = $this->get(route('api.hello'));

        $this->assertEquals(now()->timestamp, Redis::get('lastseen:127.0.0.1'));
    }
}
