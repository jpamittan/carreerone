<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Services\EmailService;
use App\Models\Services\CronLoggingService;
use App\Models\Repositories\EmailRepository;
use Config;

class SyncPortal extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ins:syncportal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull Job applied data to POrtal )Draft)';

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
            $service = app()->make('App\Models\Services\CrmSyncDataService');
            $service->synchWithPortalJobsApplied();
            $service = app()->make('App\Models\Services\UserImportService');
            $service->synchWithPortalUserCategory();
            $logging->complete($logId);
        } catch (\Exception $e) {
            $logging->error($logId, $e);
        }
    }
}
