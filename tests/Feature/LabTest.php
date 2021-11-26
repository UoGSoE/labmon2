<?php

namespace Tests\Feature;

use App\Http\Livewire\LabList;
use App\Http\Livewire\LabNameEditor;
use App\Http\Livewire\NewLabEditor;
use App\Models\Lab;
use App\Models\Machine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

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
            ->set('school', 'Science')
            ->call('saveLab')
            ->assertSet('labName', '')
            ->assertSet('editing', false)
            ->assertEmitted('labAdded');
        $this->assertEquals('LABLAB', Lab::first()->name);
        $this->assertEquals('Science', Lab::first()->school);
    }

    /** @test */
    public function we_can_delete_an_existing_lab()
    {
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
            'ips' => "127.0.0.1\r\n1.2.3.4\r\n",
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
        $machine1 = Machine::factory()->create(['ip' => '1.2.3.4', 'lab_id' => $lab->id]);
        $machine2 = Machine::factory()->create(['ip' => '127.0.0.1', 'lab_id' => $lab->id]);
        $response = $this->post(route('lab.members.update', $lab->id), [
            'ips' => "127.0.0.1\r\n1.0.3.4\r\n",
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
        $machine1 = Machine::factory()->create(['ip' => '1.2.3.4', 'lab_id' => $lab->id]);
        $machine2 = Machine::factory()->create(['ip' => '127.0.0.1', 'lab_id' => $lab->id]);
        $response = $this->post(route('lab.members.update', $lab->id), [
            'ips' => "127.0.0.1\r\n\r\n\r\n1.0.3.4\r\n\r\n\r\n",
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
        $machine1 = Machine::factory()->create(['ip' => '1.2.3.4', 'lab_id' => $lab->id]);
        $machine2 = Machine::factory()->create(['ip' => '127.0.0.1', 'lab_id' => $lab->id]);
        $response = $this->post(route('lab.members.update', $lab->id), [
            'ips' => "fred\r\n\r\n\r\n1.0.3.4\r\n\r\n\r\n",
        ]);

        $response->assertRedirect(route('lab.show', $lab->id));
        tap($lab->fresh(), function ($lab) {
            $this->assertCount(1, $lab->members);
            $this->assertTrue($lab->members->contains('ip', '1.0.3.4'));
        });
    }

    /** @test */
    public function we_can_toggle_a_labs_availability_for_remote_access()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($this->createUser());
        $lab = $this->createLab('blah');
        $lab->always_remote_access = false;
        $lab->limited_remote_access = false;
        $lab->save();

        $this->assertFalse($lab->limited_remote_access);
        $this->assertFalse($lab->always_remote_access);

        Livewire::test(LabList::class)
            ->assertSee($lab->name)
            ->call('toggleLimitedRemote', $lab->id);

        $this->assertTrue($lab->fresh()->limited_remote_access);
        $this->assertFalse($lab->fresh()->always_remote_access);

        Livewire::test(LabList::class)
            ->assertSee($lab->name)
            ->call('toggleLimitedRemote', $lab->id);

        $this->assertFalse($lab->fresh()->limited_remote_access);
        $this->assertFalse($lab->fresh()->always_remote_access);

        Livewire::test(LabList::class)
            ->assertSee($lab->name)
            ->call('toggleAlwaysRemote', $lab->id);

        $this->assertFalse($lab->fresh()->limited_remote_access);
        $this->assertTrue($lab->fresh()->always_remote_access);

        Livewire::test(LabList::class)
            ->assertSee($lab->name)
            ->call('toggleAlwaysRemote', $lab->id);

        $this->assertFalse($lab->fresh()->limited_remote_access);
        $this->assertFalse($lab->fresh()->always_remote_access);
    }

    /** @test */
    public function a_lab_cannot_be_both_limited_and_unlimited_access_at_the_same_time()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($this->createUser());
        $lab = $this->createLab('blah');
        $lab->always_remote_access = true;
        $lab->limited_remote_access = false;
        $lab->save();

        Livewire::test(LabList::class)
            ->assertSee($lab->name)
            ->call('toggleLimitedRemote', $lab->id);

        $this->assertTrue($lab->fresh()->limited_remote_access);
        $this->assertFalse($lab->fresh()->always_remote_access);

        Livewire::test(LabList::class)
            ->assertSee($lab->name)
            ->call('toggleAlwaysRemote', $lab->id);

        $this->assertFalse($lab->fresh()->limited_remote_access);
        $this->assertTrue($lab->fresh()->always_remote_access);
    }

    protected function createLab($name = 'whatevs')
    {
        return Lab::factory()->create(['name' => $name]);
    }
}
