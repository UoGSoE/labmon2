<?php

namespace Tests\Feature;

use App\Lab;
use App\User;
use App\Machine;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class MachineListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_a_list_of_all_machines()
    {
        $user = factory(User::class)->create(['is_allowed' => true]);
        $machines = factory(Machine::class, 10)->create();

        $response = $this->actingAs($user)->get(route('machine.index'));

        $response->assertOk();
        $machines->each(function ($machine) use ($response) {
            $response->assertSee($machine->name);
        });
    }

    /** @test */
    public function we_can_see_a_list_of_all_machines_for_a_specific_lab()
    {
        $user = factory(User::class)->create(['is_allowed' => true]);
        $lab = factory(Lab::class)->create();
        $labMachines = factory(Machine::class, 3)->create(['lab_id' => $lab->id]);
        $otherMachines = factory(Machine::class, 3)->create(['lab_id' => null]);

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
        $user = factory(User::class)->create(['is_allowed' => true]);
        $machine1 = factory(Machine::class)->create(['name' => 'test.example.com', 'ip' => '1.1.1.1']);
        $machine2 = factory(Machine::class)->create(['name' => 'jenny.something.org', 'ip' => '2.2.2.2']);

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
        $user = factory(User::class)->create(['is_allowed' => true]);
        $machine1 = factory(Machine::class)->create(['name' => 'test.example.com', 'ip' => '1.1.1.1', 'meta' => 'blah']);
        $machine2 = factory(Machine::class)->create(['name' => 'jenny.something.org', 'ip' => '2.2.2.2', 'meta' => 'spoons']);

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
        $user = factory(User::class)->create(['is_allowed' => true]);
        $machine1 = factory(Machine::class)->create(['name' => 'test.example.com', 'is_locked' => true, 'logged_in' => false]);
        $machine2 = factory(Machine::class)->create(['name' => 'jenny.something.org', 'is_locked' => false, 'logged_in' => false]);
        $machine3 = factory(Machine::class)->create(['name' => 'cat.example.com', 'is_locked' => false, 'logged_in' => true]);
        $machine4 = factory(Machine::class)->create(['name' => 'dog.something.org', 'is_locked' => false, 'logged_in' => false]);

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
