<?php
namespace App\Console\Commands;

use App\Models\Entities\User;
use App\Models\Services\CronLoggingService;
use App\Models\Services\UserImportService;
use Illuminate\Console\Command;

class ImportUsers extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ins:import_users';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import INS Users';

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
            $service = app()->make('App\Models\Services\UserImportService');
            $service->processUserImport();
            $logging->complete($logId);
            $this->info($logId);
        } catch (\Exception $e) {
            $logging->error($logId, $e);
        }
    }
}
