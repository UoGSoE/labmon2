<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

test('we can see the options page', function () {
    $this->withoutExceptionHandling();
    $user = $this->createUser();
    $response = $this->actingAs($user)->get(route('options.edit'));

    $response->assertOk();
    $response->assertSee('Options');
});

test('we can update all of the options', function () {
    $this->withoutExceptionHandling();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create(['is_allowed' => true]);
    $user3 = User::factory()->create();
    $user4 = User::factory()->create(['is_allowed' => true]);

    expect($user1->fresh()->isAllowedAccess())->toBeFalse();
    expect($user2->fresh()->isAllowedAccess())->toBeTrue();
    expect($user3->fresh()->isAllowedAccess())->toBeFalse();
    expect($user4->fresh()->isAllowedAccess())->toBeTrue();

    $response = $this->actingAs($user2)->post(route('options.update'), [
        'remote-start-hour' => 20,
        'remote-end-hour' => 10,
        'remote-summer' => '04/Feb - 11/Mar',
        'remote-xmas' => '04/Dec - 31/Dec',
        'remote-easter' => '04/Apr - 10/Apr',
        'allowed_guids' => "{$user1->username}\r\n{$user3->username}\r\n",
    ]);

    $response->assertRedirect('/');
    $response->assertSessionDoesntHaveErrors();
    expect(option('remote-start-hour'))->toEqual('20');
    expect(option('remote-end-hour'))->toEqual('10');
    expect(option('remote-start-summer'))->toEqual('04/Feb');
    expect(option('remote-end-summer'))->toEqual('11/Mar');
    expect(option('remote-start-xmas'))->toEqual('04/Dec');
    expect(option('remote-end-xmas'))->toEqual('31/Dec');
    expect(option('remote-start-easter'))->toEqual('04/Apr');
    expect(option('remote-end-easter'))->toEqual('10/Apr');
    expect($user1->fresh()->isAllowedAccess())->toBeTrue();
    // note: users can't remove themselves from the permission list
    // so user2 is still allowed access even though they weren't in
    // the supplied allowed_guids
    expect($user2->fresh()->isAllowedAccess())->toBeTrue();
    expect($user3->fresh()->isAllowedAccess())->toBeTrue();
    expect($user4->fresh()->isAllowedAccess())->toBeFalse();
});
