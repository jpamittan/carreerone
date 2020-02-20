<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ImportUsers::class,
        Commands\RssFeeds::class,
    	Commands\ImportJobs::class,
    	Commands\ImportCrmLookupData::class,
    	Commands\SyncCRM::class,
        Commands\ImportJobMatch::class,
        Commands\CapabilityMatch::class,
        Commands\PushJobCapabilities::class,
        Commands\PushUserCapabilities::class,
        Commands\ScheduleInterview::class,
        Commands\ExpiredJob::class,
        Commands\EmailCandidate::class,
        Commands\ImportCaseManager::class,
        Commands\jobTitleSkillMatch::class,
        Commands\PushUsers::class,
        Commands\InterviewReminderEmailCandidate::class,
        Commands\ImportJobMatchEOI::class,
        Commands\SyncPortal::class,
        Commands\JobRDUploadReminder::class,
        Commands\PushUserSkills::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        $schedule->command('inspire')->hourly();
    }
}
