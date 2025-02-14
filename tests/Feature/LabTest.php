<?php

use App\Livewire\LabList;
use App\Livewire\LabNameEditor;
use App\Livewire\NewLabEditor;
use App\Models\Lab;
use App\Models\Machine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

test('we can create a new lab', function () {
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
        ->assertDispatched('labAdded');
    $this->assertEquals('LABLAB', Lab::first()->name);
    $this->assertEquals('Science', Lab::first()->school);
});

test('we can delete an existing lab', function () {
    $this->actingAs($this->createUser());
    $lab = createLab('My Amazing Lab');

    Livewire::test(LabNameEditor::class, ['lab' => $lab])
        ->assertSee($lab->name)
        ->set('editing', true)
        ->assertSee('Delete')
        ->call('deleteLab')
        ->assertDontSee('Delete')
        ->assertSee('Confirm')
        ->call('deleteLab');
    $this->assertCount(0, Lab::all());
});

test('we can add machine ips to a lab', function () {
    $this->actingAs($this->createUser());
    $lab = createLab('blah');

    $response = $this->post(route('lab.members.update', $lab->id), [
        'ips' => "127.0.0.1\r\n1.2.3.4\r\n",
    ]);

    $response->assertRedirect(route('lab.show', $lab->id));
    tap($lab->fresh(), function ($lab) {
        $this->assertCount(2, $lab->members);
        $this->assertTrue($lab->members->contains('ip', '1.2.3.4'));
        $this->assertTrue($lab->members->contains('ip', '127.0.0.1'));
    });
});

test('sending a list of labmember ips removes any not on the list', function () {
    $this->withoutExceptionHandling();
    $this->actingAs($this->createUser());
    $lab = createLab('blah');
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
});

test('blank lines in the list of ips are ignored', function () {
    $this->withoutExceptionHandling();
    $this->actingAs($this->createUser());
    $lab = createLab('blah');
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
});

test('invalid ips are ignored', function () {
    $this->withoutExceptionHandling();
    $this->actingAs($this->createUser());
    $lab = createLab('blah');
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
});

test('we can toggle a labs availability for remote access', function () {
    $this->withoutExceptionHandling();
    $this->actingAs($this->createUser());
    $lab = createLab('blah');
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
});

test('a lab cannot be both limited and unlimited access at the same time', function () {
    $this->withoutExceptionHandling();
    $this->actingAs($this->createUser());
    $lab = createLab('blah');
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
});

// Helpers
function createLab($name = 'whatevs')
{
    return Lab::factory()->create(['name' => $name]);
}
