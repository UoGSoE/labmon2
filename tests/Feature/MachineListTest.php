<?php

namespace Tests\Feature;

use App\Lab;
use App\Machine;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class MachineListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_a_list_of_all_machines()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create(['is_allowed' => true]);
        $machines = Machine::factory()->count(10)->create();

        $response = $this->actingAs($user)->get(route('machine.index'));

        $response->assertOk();
        $machines->each(function ($machine) use ($response) {
            $response->assertSee($machine->name);
        });
    }

    /** @test */
    public function we_can_see_a_list_of_all_machines_for_a_specific_lab()
    {
        $user = User::factory()->create(['is_allowed' => true]);
        $lab = Lab::factory()->create();
        $labMachines = Machine::factory()->count(3)->create(['lab_id' => $lab->id]);
        $otherMachines = Machine::factory()->count(3)->create(['lab_id' => null]);

        $response = $this->actingAs($user)->get(route('lab.show', $lab->id));

        $response->assertOk();
        $labMachines->each(function ($machine) use ($response) {
            $response->assertSee($machine->name);
        });
        $otherMachines->each(function ($machine) use ($response) {
            $response->assertDontSee($machine->name);
        });
    }

    /** @test */
    public function we_can_filter_the_list_of_machines_by_name_or_ip_address()
    {
        $user = User::factory()->create(['is_allowed' => true]);
        $machine1 = Machine::factory()->create(['name' => 'test.example.com', 'ip' => '1.1.1.1']);
        $machine2 = Machine::factory()->create(['name' => 'jenny.something.org', 'ip' => '2.2.2.2']);

        $this->actingAs($user);

        Livewire::test('machine-list', ['machines' => [$machine1, $machine2]])
            ->assertSee('test.example.com')
            ->assertSee('jenny.something.org')
            ->set('filter', 'test')
            ->assertSee('test.example.com')
            ->assertDontSee('jenny.something.org')
            ->set('filter', '2.2.2')
            ->assertDontSee('test.example.com')
            ->assertSee('jenny.something.org');
    }

    /** @test */
    public function we_can_optionally_filter_the_list_of_machines_by_meta_data()
    {
        $user = User::factory()->create(['is_allowed' => true]);
        $machine1 = Machine::factory()->create(['name' => 'test.example.com', 'ip' => '1.1.1.1', 'meta' => 'blah']);
        $machine2 = Machine::factory()->create(['name' => 'jenny.something.org', 'ip' => '2.2.2.2', 'meta' => 'spoons']);

        $this->actingAs($user);

        Livewire::test('machine-list', ['machines' => [$machine1, $machine2]])
            ->assertSee('test.example.com')
            ->assertSee('jenny.something.org')
            ->set('filter', 'spoons')
            ->assertDontSee('test.example.com')
            ->assertDontSee('jenny.something.org')
            ->set('includeMeta', true)
            ->assertSee('jenny.something.org');
    }

    /** @test */
    public function we_can_filter_the_list_of_machines_by_their_status()
    {
        $user = User::factory()->create(['is_allowed' => true]);
        $machine1 = Machine::factory()->create(['name' => 'test.example.com', 'is_locked' => true, 'logged_in' => false]);
        $machine2 = Machine::factory()->create(['name' => 'jenny.something.org', 'is_locked' => false, 'logged_in' => false]);
        $machine3 = Machine::factory()->create(['name' => 'cat.example.com', 'is_locked' => false, 'logged_in' => true]);
        $machine4 = Machine::factory()->create(['name' => 'dog.something.org', 'is_locked' => false, 'logged_in' => false]);

        $this->actingAs($user);

        Livewire::test('machine-list', ['machines' => [$machine1, $machine2, $machine3, $machine4]])
            ->assertSee('test.example.com')
            ->assertSee('jenny.something.org')
            ->assertSee('cat.example.com')
            ->assertSee('dog.something.org')
            ->set('statusFilter', 'locked')
            ->assertSee('test.example.com')
            ->assertDontSee('jenny.something.org')
            ->assertDontSee('cat.example.com')
            ->assertDontSee('dog.something.org')
            ->set('statusFilter', 'not_locked')
            ->assertDontSee('test.example.com')
            ->assertSee('jenny.something.org')
            ->assertSee('cat.example.com')
            ->assertSee('dog.something.org')
            ->set('statusFilter', 'logged_in')
            ->assertDontSee('test.example.com')
            ->assertDontSee('jenny.something.org')
            ->assertSee('cat.example.com')
            ->assertDontSee('dog.something.org')
            ->set('statusFilter', 'not_logged_in')
            ->assertSee('test.example.com')
            ->assertSee('jenny.something.org')
            ->assertDontSee('cat.example.com')
            ->assertSee('dog.something.org')
            ->set('statusFilter', '')
            ->assertSee('test.example.com')
            ->assertSee('jenny.something.org')
            ->assertSee('cat.example.com')
            ->assertSee('dog.something.org');
    }
}
