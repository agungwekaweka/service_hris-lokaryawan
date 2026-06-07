<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\updateMasaBerlakuCuti::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('insert:karyawan')
        ->dailyAt('00:01');

        $schedule->command('update:komplement')
        ->dailyAt('00:02');

        $schedule->command('update:masaBerlakuKomplement')
        ->dailyAt('23:00');

        $schedule->command('update:masaBerlakuCuti')
        ->dailyAt('00:04');

        $schedule->command('reject:cutiExpied')
        ->dailyAt('00:00');

        $schedule->command('reject:overtimeExpied')
        ->dailyAt('00:01');

        $schedule->command('cron:requestCutiAutoUpload')
        ->dailyAt('00:03');
        
        $schedule->command('notif:cutiTahunanLampiran')
        ->dailyAt('00:05');

       
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
