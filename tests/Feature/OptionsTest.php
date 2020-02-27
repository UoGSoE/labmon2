<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OptionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_the_options_page()
    {
        $response = $this->get(route('options.edit'));

        $response->assertOk();
        $response->assertSee('Options');
    }

    /** @test */
    public function we_can_update_all_of_the_options()
    {
        // $this->withoutExceptionHandling();
        $response = $this->post(route('options.update'), [
            'remote-start-hour' => 20,
            'remote-end-hour' => 10,
            'remote-summer' => '04/Feb - 11/Mar',
            'remote-xmas' => '04/Dec - 31/Dec',
            'remote-easter' => '04/Apr - 10/Apr',
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
    }
}
