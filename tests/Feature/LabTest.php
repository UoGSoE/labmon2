<?php

namespace Tests\Feature;

use App\Lab;
use App\Machine;
use Tests\TestCase;
use Livewire\Livewire;
use App\Http\Livewire\NewLabEditor;
use App\Http\Livewire\LabNameEditor;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LabTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_create_a_new_lab()
    {
        $this->actingAs($this->createUser());
        Livewire::test(NewLabEditor::class)
            ->assertSee('Add new lab')
            ->set('editing', true)
            ->assertDontSee('Add new lab')
            ->assertSee('Save')
            ->set('labName', 'LABLAB')
            ->call('saveLab')
            ->assertSet('labName', '')
            ->assertSet('editing', false)
            ->assertEmitted('labAdded');
        $this->assertEquals('LABLAB', Lab::first()->name);
    }

    /** @test */
    public function we_can_delete_an_existing_lab()
    {
        $this->markTestSkipped('See https://github.com/livewire/livewire/issues/649');
        $this->actingAs($this->createUser());
        $lab = $this->createLab('My Amazing Lab');

        Livewire::test(LabNameEditor::class, ['lab' => $lab])
            ->assertSee($lab->name)
            ->set('editing', true)
            ->assertSee('Delete')
            ->call('deleteLab')
            ->assertDontSee('Delete')
            ->assertSee('Confirm')
            ->call('deleteLab');
        $this->assertCount(0, Lab::all());
    }

    /** @test */
    public function we_can_add_machine_ips_to_a_lab()
    {
        $this->actingAs($this->createUser());
        $lab = $this->createLab('blah');

        $response = $this->post(route('lab.members.update', $lab->id), [
            'ips' => "127.0.0.1\r\n1.2.3.4\r\n"
        ]);

        $response->assertRedirect(route('lab.show', $lab->id));
        tap($lab->fresh(), function ($lab) {
            $this->assertCount(2, $lab->members);
            $this->assertTrue($lab->members->contains('ip', '1.2.3.4'));
            $this->assertTrue($lab->members->contains('ip', '127.0.0.1'));
        });
    }

    /** @test */
    public function sending_a_list_of_labmember_ips_removes_any_not_on_the_list()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($this->createUser());
        $lab = $this->createLab('blah');
        $machine1 = factory(Machine::class)->create(['ip' => '1.2.3.4', 'lab_id' => $lab->id]);
        $machine2 = factory(Machine::class)->create(['ip' => '127.0.0.1', 'lab_id' => $lab->id]);
        $response = $this->post(route('lab.members.update', $lab->id), [
            'ips' => "127.0.0.1\r\n1.0.3.4\r\n"
        ]);

        $response->assertRedirect(route('lab.show', $lab->id));
        tap($lab->fresh(), function ($lab) {
            $this->assertCount(2, $lab->members);
            $this->assertTrue($lab->members->contains('ip', '1.0.3.4'));
            $this->assertTrue($lab->members->contains('ip', '127.0.0.1'));
        });
    }

    /** @test */
    public function blank_lines_in_the_list_of_ips_are_ignored()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($this->createUser());
        $lab = $this->createLab('blah');
        $machine1 = factory(Machine::class)->create(['ip' => '1.2.3.4', 'lab_id' => $lab->id]);
        $machine2 = factory(Machine::class)->create(['ip' => '127.0.0.1', 'lab_id' => $lab->id]);
        $response = $this->post(route('lab.members.update', $lab->id), [
            'ips' => "127.0.0.1\r\n\r\n\r\n1.0.3.4\r\n\r\n\r\n"
        ]);

        $response->assertRedirect(route('lab.show', $lab->id));
        tap($lab->fresh(), function ($lab) {
            $this->assertCount(2, $lab->members);
            $this->assertTrue($lab->members->contains('ip', '1.0.3.4'));
            $this->assertTrue($lab->members->contains('ip', '127.0.0.1'));
        });
    }

    /** @test */
    public function invalid_ips_are_ignored()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($this->createUser());
        $lab = $this->createLab('blah');
        $machine1 = factory(Machine::class)->create(['ip' => '1.2.3.4', 'lab_id' => $lab->id]);
        $machine2 = factory(Machine::class)->create(['ip' => '127.0.0.1', 'lab_id' => $lab->id]);
        $response = $this->post(route('lab.members.update', $lab->id), [
            'ips' => "fred\r\n\r\n\r\n1.0.3.4\r\n\r\n\r\n"
        ]);

        $response->assertRedirect(route('lab.show', $lab->id));
        tap($lab->fresh(), function ($lab) {
            $this->assertCount(1, $lab->members);
            $this->assertTrue($lab->members->contains('ip', '1.0.3.4'));
        });
    }

    protected function createLab($name = 'whatevs')
    {
        return factory(Lab::class)->create(['name' => $name]);
    }
}
