<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OptionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_the_options_page(): void
    {
        $this->withoutExceptionHandling();
        $user = $this->createUser();
        $response = $this->actingAs($user)->get(route('options.edit'));

        $response->assertOk();
        $response->assertSee('Options');
    }

    /** @test */
    public function we_can_update_all_of_the_options(): void
    {
        $this->withoutExceptionHandling();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create(['is_allowed' => true]);
        $user3 = User::factory()->create();
        $user4 = User::factory()->create(['is_allowed' => true]);

        $this->assertFalse($user1->fresh()->isAllowedAccess());
        $this->assertTrue($user2->fresh()->isAllowedAccess());
        $this->assertFalse($user3->fresh()->isAllowedAccess());
        $this->assertTrue($user4->fresh()->isAllowedAccess());

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
        $this->assertEquals('20', option('remote-start-hour'));
        $this->assertEquals('10', option('remote-end-hour'));
        $this->assertEquals('04/Feb', option('remote-start-summer'));
        $this->assertEquals('11/Mar', option('remote-end-summer'));
        $this->assertEquals('04/Dec', option('remote-start-xmas'));
        $this->assertEquals('31/Dec', option('remote-end-xmas'));
        $this->assertEquals('04/Apr', option('remote-start-easter'));
        $this->assertEquals('10/Apr', option('remote-end-easter'));
        $this->assertTrue($user1->fresh()->isAllowedAccess());
        // note: users can't remove themselves from the permission list
        // so user2 is still allowed access even though they weren't in
        // the supplied allowed_guids
        $this->assertTrue($user2->fresh()->isAllowedAccess());
        $this->assertTrue($user3->fresh()->isAllowedAccess());
        $this->assertFalse($user4->fresh()->isAllowedAccess());
    }
}
