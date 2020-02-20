<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportCaseManager extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ins:import_case_manager';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import INS Case Managers';

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
       $service = app()->make('App\Models\Services\CaseManagerService');
       $service->processCaseManager();
    }
}
