<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Asserts that a command is registered with the console kernel schedular.
     *
     * @param  string  $command  The artisan-format command (eg 'myapp:do-a-thing')
     * @return void
     */
    protected function assertCommandIsScheduled(string $command)
    {
        $schedular = app(\Illuminate\Console\Scheduling\Schedule::class);
        $this->assertTrue(collect($schedular->events())->contains(function ($task) use ($command) {
            return preg_match("/{$command}$/", $task->command) === 1;
        }), "Command {$command} is not registered with the schedular");
    }

    protected function createUser($overrides = ['is_allowed' => true])
    {
        return User::factory()->create($overrides);
    }
}
