<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PushJobCapabilities extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ins:jobcapability';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push Job Capabilities upoaded by the recruiter';

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
        )->hours(15)->minutes(0);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
       $service = app()->make('App\Models\Services\PushJobCapability');
       $service->pushCapability();
    }
}
