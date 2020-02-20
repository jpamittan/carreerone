<?php

namespace App\Models\Repositories;

use App\Models\Repositories\RepositoryBase;
use App\Models\Entities\User;
use App\Models\Entities\Jobs;
use App\Models\Factories\ExternalFileFactory;
use App\Models\Repositories\EmailRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB, Config, Redirect, Session, URL, Validator, View;

class ExpiredJobRepository extends RepositoryBase {
	public function chkExpiredJobs() {
		$expiredJobs = Jobs::select('ins_jobs.*', 'ins_agency_details.agency_name')
			->leftJoin('ins_agency_details', function($join) {
				$join->on('ins_agency_details.id', '=', 'ins_jobs.agency_id');
			})
			->where('ins_jobs.deadline_date', '<', date('Y-m-d'))
			->where('ins_jobs.is_expired', '=', null)
			->where('ins_agency_details.is_active' ,'1')
			->get();
		return $expiredJobs;
	}

	public function updateExpiredJob($object) {
		$jobExpired = Jobs::find($object->id);
		$jobExpired->is_expired = date('Y-m-d H:i:s');
        $jobExpired->save();
		return $jobExpired;
	}
	
	public function sendExpiredEmail($object) {
		$emailService = new EmailRepository();
		$message = View::make('site/email/expired-job',array('details' => $object ));
		$subject = 'Expired Job';
		$to = $object->prepared_by_email;
		$from = Config::get('ins_emails.expired_jobs.from');
		$emailService->send($message,$subject,$from,$to);
	}
}
