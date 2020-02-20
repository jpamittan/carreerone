<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Services\CronLoggingService;

class ImportCrmLookupData extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ins:import_crmlookupdata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import INS CRM Lookup data (Locations/Categories/JobCategoryTypes/Agencies )';

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
        $logging = new CronLoggingService();
        $logId = $logging->start($this->signature);
        try {
            $service = app()->make('App\Models\Services\CrmLookupDataImportService');
            $service->processCrmLookupData();
            $logging->complete($logId);
        } catch (\Exception $e) {
            $logging->error($logId, $e);
        }
    }
}
