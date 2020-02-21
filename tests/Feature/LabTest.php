<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LabTest extends TestCase
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
    public function we_can_create_a_new_lab()
    {
        $response = $this->postJson(route('api.lab.store', [
            'name' => 'My Amazing Lab',
        ]));

        $response->assertOk();
        $this->assertCount(1, Redis::smembers('labs'));
        $this->assertEquals('My Amazing Lab', Redis::smembers('labs')[0]);
    }

    /** @test */
    public function we_can_delete_an_existing_lab()
    {
        $this->createLab('My Amazing Lab');

        $response = $this->deleteJson(route('api.lab.destroy', [
            'name' => 'My Amazing Lab',
        ]));

        $response->assertOk();
        $this->assertCount(0, Redis::smembers('labs'));
    }

    /** @test */
    public function we_can_add_machine_ips_to_a_lab()
    {
        $this->createLab('blah');

        $response = $this->postJson(route('api.lab.machines.update', 'blah'), [
            'ips' => [
                ['ip' => '127.0.0.1'],
                ['ip' => '1.2.3.4'],
            ]
        ]);

        $response->assertOk();
        $this->assertCount(2, Redis::smembers("lab:blah"));
        $this->assertTrue(collect(Redis::smembers("lab:blah"))->contains("127.0.0.1"));
        $this->assertTrue(collect(Redis::smembers("lab:blah"))->contains("1.2.3.4"));
    }

    /** @test */
    public function sending_an_empty_list_of_ips_removes_the_lab_entry_itself()
    {
        $this->withoutExceptionHandling();
        $this->createLab('blah');

        $response = $this->postJson(route('api.lab.machines.update', 'blah'), [
            'ips' => [
                ['ip' => '127.0.0.1'],
                ['ip' => '1.2.3.4'],
            ]
        ]);

        $this->assertTrue((bool) Redis::exists("lab:blah"));

        $response = $this->postJson(route('api.lab.machines.update', 'blah'), [
            'ips' => [],
        ]);

        $response->assertOk();
        $this->assertFalse((bool) Redis::exists("lab:blah"));
    }

    protected function createLab($name = 'whatevs')
    {
        Redis::sadd('labs', $name);
    }
}
