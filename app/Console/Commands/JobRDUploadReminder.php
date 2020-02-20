<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class JobRDUploadReminder extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ins:job_rd_upload_reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Role desc upload reminder 2nd and 3rd. TO be run at 9am every day';

    public function __construct() {
        parent::__construct();
    }

    /**
     * When a command should run
     *
     * @param Scheduler $scheduler
     * @return \Indatus\Dispatcher\Scheduling\Schedulable
     */
    public function schedule(Schedulable $scheduler) {
        return $scheduler->opts(
            array(
                "env" => App::environment()
            )
        )->hours(9)->minutes(0);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
       $service = app()->make('App\Models\Services\JobMatchImportService');
       $service->processJobRDUpload();
    }
}
