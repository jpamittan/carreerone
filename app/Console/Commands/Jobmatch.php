<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class Jobmatch extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ins:jobmatch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display an Jobmatch between candidate resume and Job';

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
        )->hours(14)->minutes(0);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $service = app()->make('App\Http\Controllers\site\JobController');
        $service->jobMatch();
    }
}
