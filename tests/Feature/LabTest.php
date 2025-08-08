<?php

use App\Livewire\Pages\LabIndex;
use App\Livewire\Pages\LabShow;
use App\Models\Lab;
use App\Models\Machine;
use Livewire\Livewire;

test('we can create a new lab', function () {
    $this->actingAs($this->createUser());
    Livewire::test(LabIndex::class)
        ->assertSee('Add new lab')
        ->set('editing', true)
        ->assertDontSee('Add new lab')
        ->assertSee('Save')
        ->set('labName', 'LABLAB')
        ->set('school', 'Science')
        ->call('saveLab')
        ->assertSet('labName', '')
        ->assertSet('editing', false);
    expect(Lab::first()->name)->toEqual('LABLAB');
    expect(Lab::first()->school)->toEqual('Science');
});

test('we can delete an existing lab', function () {
    $this->actingAs($this->createUser());
    $lab = createLab('My Amazing Lab');

    Livewire::test(LabShow::class, ['lab' => $lab])
        ->assertSee($lab->name)
        ->set('editing', true)
        ->assertSee('Delete')
        ->call('deleteLab')
        ->assertDontSee('Delete')
        ->assertSee('Confirm')
        ->call('deleteLab');
    expect(Lab::all())->toHaveCount(0);
});

test('we can add machine ips to a lab', function () {
    $this->actingAs($this->createUser());
    $lab = createLab('blah');

    $response = $this->post(route('lab.members.update', $lab->id), [
        'ips' => "127.0.0.1\r\n1.2.3.4\r\n",
    ]);

    $response->assertRedirect(route('lab.show', $lab->id));
    tap($lab->fresh(), function ($lab) {
        expect($lab->members)->toHaveCount(2);
        expect($lab->members->contains('ip', '1.2.3.4'))->toBeTrue();
        expect($lab->members->contains('ip', '127.0.0.1'))->toBeTrue();
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
        expect($lab->members)->toHaveCount(2);
        expect($lab->members->contains('ip', '1.0.3.4'))->toBeTrue();
        expect($lab->members->contains('ip', '127.0.0.1'))->toBeTrue();
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
        expect($lab->members)->toHaveCount(2);
        expect($lab->members->contains('ip', '1.0.3.4'))->toBeTrue();
        expect($lab->members->contains('ip', '127.0.0.1'))->toBeTrue();
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
        expect($lab->members)->toHaveCount(1);
        expect($lab->members->contains('ip', '1.0.3.4'))->toBeTrue();
    });
});

test('we can toggle a labs availability for remote access', function () {
    $this->withoutExceptionHandling();
    $this->actingAs($this->createUser());
    $lab = createLab('blah');
    $lab->always_remote_access = false;
    $lab->limited_remote_access = false;
    $lab->save();

    expect($lab->limited_remote_access)->toBeFalse();
    expect($lab->always_remote_access)->toBeFalse();

    Livewire::test(LabIndex::class)
        ->assertSee($lab->name)
        ->call('toggleLimitedRemote', $lab->id);

    expect($lab->fresh()->limited_remote_access)->toBeTrue();
    expect($lab->fresh()->always_remote_access)->toBeFalse();

    Livewire::test(LabIndex::class)
        ->assertSee($lab->name)
        ->call('toggleLimitedRemote', $lab->id);

    expect($lab->fresh()->limited_remote_access)->toBeFalse();
    expect($lab->fresh()->always_remote_access)->toBeFalse();

    Livewire::test(LabIndex::class)
        ->assertSee($lab->name)
        ->call('toggleAlwaysRemote', $lab->id);

    expect($lab->fresh()->limited_remote_access)->toBeFalse();
    expect($lab->fresh()->always_remote_access)->toBeTrue();

    Livewire::test(LabIndex::class)
        ->assertSee($lab->name)
        ->call('toggleAlwaysRemote', $lab->id);

    expect($lab->fresh()->limited_remote_access)->toBeFalse();
    expect($lab->fresh()->always_remote_access)->toBeFalse();
});

test('a lab cannot be both limited and unlimited access at the same time', function () {
    $this->withoutExceptionHandling();
    $this->actingAs($this->createUser());
    $lab = createLab('blah');
    $lab->always_remote_access = true;
    $lab->limited_remote_access = false;
    $lab->save();

    Livewire::test(LabIndex::class)
        ->assertSee($lab->name)
        ->call('toggleLimitedRemote', $lab->id);

    expect($lab->fresh()->limited_remote_access)->toBeTrue();
    expect($lab->fresh()->always_remote_access)->toBeFalse();

    Livewire::test(LabIndex::class)
        ->assertSee($lab->name)
        ->call('toggleAlwaysRemote', $lab->id);

    expect($lab->fresh()->limited_remote_access)->toBeFalse();
    expect($lab->fresh()->always_remote_access)->toBeTrue();
});

// Helpers
function createLab($name = 'whatevs')
{
    return Lab::factory()->create(['name' => $name]);
}
