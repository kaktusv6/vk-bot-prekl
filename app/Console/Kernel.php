<?php

namespace App\Console;

use App\Modules\Events\Commands\PeerPollsCreator;
use App\Modules\Events\Commands\ReportAboutUsers;
use App\Modules\Events\Commands\SimpleEvent;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule
            ->command(ReportAboutUsers::class)
            ->weeklyOn(1, '10:00');

        $schedule
            ->command(PeerPollsCreator::class)
            ->weeklyOn(4, '12:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->registerCommand(app(SimpleEvent::class));
        $this->registerCommand(app(ReportAboutUsers::class));
        $this->registerCommand(app(PeerPollsCreator::class));
    }
}
