<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Repositories\EmailRepository;
use App\Models\Repositories\UserPushRepository;
use App\Models\Repositories\ResumeRepository;
use App\Models\Entities\User;

class PushUserSkills extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ins:push_user_skills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push User Skills';

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
        $user = User::where('email', 'adil.yaqoob@careerone.com.au')->first()  ;
        $this->emailService = new \App\Models\Services\EmailService(new \App\Models\Repositories\EmailRepository);
        $this->emailService->sendUserActivationEmail($user);
    }
}
