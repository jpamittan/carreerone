<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class Jobcsv extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ins:readcsv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read jobs from CSV and upload jobs to database';

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
        $service = app()->make('App\Http\Controllers\site\ReadCSVController');
        $service->importCSV();
    }
}
