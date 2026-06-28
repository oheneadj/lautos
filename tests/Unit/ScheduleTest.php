<?php

namespace Tests\Unit;

use Illuminate\Console\Scheduling\Schedule;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests that the commands routes/console.php registers actually end up on
 * the schedule — this app has no Horizon/persistent worker on production,
 * so a typo here means a job silently never runs rather than erroring.
 */
class ScheduleTest extends TestCase
{
    #[Test]
    public function the_nightly_backup_commands_are_scheduled(): void
    {
        $commands = $this->scheduledCommands();

        $this->assertContains('backup:clean', $commands);
        $this->assertContains('backup:run', $commands);
        $this->assertContains('backup:monitor', $commands);
    }

    #[Test]
    public function the_existing_app_commands_are_still_scheduled(): void
    {
        $commands = $this->scheduledCommands();

        $this->assertContains('blog:publish-scheduled', $commands);
        $this->assertContains('cars:archive-sold', $commands);
        $this->assertContains('sitemap:generate', $commands);
    }

    /**
     * I pull the command name out of each scheduled event's raw command
     * string rather than matching the whole string, since the binary path
     * and PHP executable differ between local dev and the test runner.
     *
     * @return array<int, string>
     */
    private function scheduledCommands(): array
    {
        $schedule = app(Schedule::class);

        return array_map(function ($event) {
            preg_match('/artisan\' (\S+)/', $event->command, $matches);

            return $matches[1] ?? $event->command;
        }, $schedule->events());
    }
}
