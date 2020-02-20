<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Services\EmailService;
use App\Models\Repositories\EmailRepository;

class jobTitleSkillMatch extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ins:jobtitle_skillmatch';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'jobtitle skill match';
    
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
        return $scheduler->opts(array(
            "env" => App::environment()
        ))->hours(15)->minutes(0);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $data['type']  = 'similar';
        $data['query'] = 'Project Officer';
        $service = app()->make('App\Models\Services\JobTitleSkillMatch');
        $service->start($data);
    }
}