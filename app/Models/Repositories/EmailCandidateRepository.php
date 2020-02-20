<?php

namespace App\Models\Repositories;

use App\Models\Repositories\RepositoryBase;
use App\Models\Entities\User;
use App\Models\Entities\InterviewConfirmation;
use App\Models\Entities\Jobs;
use App\Models\Entities\AgencyDetails;
use App\Models\Factories\ExternalFileFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB, Config, Redirect, Session, URL, Validator, View;

class EmailCandidateRepository extends RepositoryBase {
	public function sendEmailCandidate() {
		$interviews_calandar = new InterviewConfirmation();
		$time = strtotime(date('Y-m-d').' -1 days');;      
        $dateToday = date("Y-m-d", $time);
		$result = $interviews_calandar->where('interview_date', '=', $dateToday)
			->where('status', '=', 1)
			->get();
		return $result;
	}

	public function interviewReminderEmailCandidate() {
		$interviews_calandar  = new InterviewConfirmation();
		$dateToday = date('Y-m-d'); 
		$result = $interviews_calandar->where('interview_date', '=', $dateToday)->where('status','=',1)->get();
		return $result;
	}

	public function getJobsDetails($job_id) {
		$jobs = new Jobs();
		$result = $jobs->where('id', '=', $job_id)->first();
		return $result;
	}

	public function getAgencyDetails($agency_id) {
		$agency = new AgencyDetails();
		$result = $agency->where('id', '=', $agency_id)->first();
		return $result;
	}

	public function getUserCandidate($id) {
		$user = new User();
		$result = $user->where('id', '=', $id)->first();
		return $result;
	}

	public function sendFeedback($inputs) {
		$interviews_calandar  = new InterviewConfirmation();
		$user = new User();
		$interviews_id = base64_decode($inputs['id'].'=');
		$arr_returns = array();
		$arr_returns['returns']	= false;
		$user_results = $user->where("crm_user_id", '=', $inputs['crm_id'])->first();
		if (!empty($user_results)) {
			$user_id = $user_results->id;
			$rs = $interviews_calandar->where('id', '=', $interviews_id)->where('candidate_id', '=', $user_id)->first();
			if (!empty($rs)) {
				$rs->feedback = addslashes($inputs['feedback']);
				$save = $rs->save();
				$getJobs = $this->getJobsDetails($rs->job_id);
				$agency_id = $getJobs->agency_id;
				$agencyList = $this->getAgencyDetails($agency_id);
				return $arr_returns  [
					'returns' => true,
					'prepared_by_email' => $getJobs->prepared_by_email,
					'fullname' => $user_results->first_name.' '.$user_results->last_name,
					'email' => $user_results->email,
					'agency_name' => $agencyList->agency_name
				];
			}
		}
		return $arr_returns;
	}
}
