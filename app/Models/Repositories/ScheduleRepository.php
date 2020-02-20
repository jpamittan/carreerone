<?php

namespace App\Models\Repositories;

use App\Models\Repositories\RepositoryBase;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\Entities\Role;
use App\Models\Entities\Permission;
use App\Models\Entities\User;
use App\Models\Entities\ScheduleInterview;
use View;
use URL;
use DB;
use Carbon\Carbon;

class ScheduleRepository extends RepositoryBase {
	public function getJobAssignedDates($job_id) {
		return DB::table('ins_interview_pending_dates')
		->select(['interview_dates','interview_timings','time'])
		->where('job_id','=',$job_id)
		->where('interview_dates','>=',date('Y-m-d'))
		->get();
	}

	public function getAppliedCandidates($job_id) {
		$res = DB::table('ins_job_candidate')
		->leftJoin('users', function($join) {
			$join->on('users.id', '=', 'ins_job_candidate.candidate_id');
		})
		->leftJoin('ins_jobs', function($join) {
			$join->on('ins_jobs.id', '=', 'ins_job_candidate.job_id');
		})
		->leftJoin('ins_agency_details', function($join) {
			$join->on('ins_agency_details.id', '=', 'ins_jobs.agency_id');
		})
		->leftJoin('ins_cv', function($join) {
			$join->on('ins_job_candidate.job_id', '=', 'ins_cv.job_id')->on('users.id', '=', 'ins_cv.candidate_id');
		})
		->leftJoin('ins_covering_letter', function($join) {
			$join->on('ins_job_candidate.job_id', '=', 'ins_covering_letter.job_id');
		})
		->leftJoin('ins_supporting_doc', function($join) {
			$join->on('ins_job_candidate.job_id', '=', 'ins_supporting_doc.job_id');
		})
		->where('ins_job_candidate.scheduled','=',0)
		->where('ins_job_candidate.job_id','=',$job_id)
		->select([
			'ins_job_candidate.id as ins_job_candidateid',
			'ins_job_candidate.candidate_id',
			'ins_jobs.id as job_id',
			'ins_jobs.vacancy_reference_id',
			'ins_jobs.jobid as jobid',
			'ins_jobs.prepared_by_email',
			'ins_jobs.prepared_by_name',
			'users.first_name',
			'users.last_name',
			'users.title',
			'ins_jobs.job_grade',
			'ins_jobs.job_title as job_title' ,
			'ins_agency_details.agency_name',
			'ins_cv.resume_url',
			'ins_cv.resume_name',
			'ins_cv.id as resumeID',
			'ins_covering_letter.coveringletter_url',
			'ins_covering_letter.covering_letter_name',
			'ins_covering_letter.id as coverLettID',
			'ins_supporting_doc.url as sup_url',
			'ins_supporting_doc.name as sup_name',
			'ins_supporting_doc.id as sup_id'])->orderBy('users.id','asc')->groupbY('ins_job_candidate.candidate_id')->get();
		return $res; 
	}

	public function getAppliedCandidateDet($job_id) {
		return  DB::table('ins_job_candidate')
		->leftJoin('users', function($join)
		{
			
			$join->on('users.id', '=', 'ins_job_candidate.candidate_id');
		})->leftJoin('ins_jobs', function($join)
		{
			$join->on('ins_jobs.id', '=', 'ins_job_candidate.job_id');
		})->leftJoin('ins_agency_details', function($join)
		{

			$join->on('ins_agency_details.id', '=', 'ins_jobs.agency_id');
		})->leftJoin('ins_cv', function($join)
		{

			$join->on('ins_job_candidate.job_id', '=', 'ins_cv.job_id');
		})
		->leftJoin('ins_covering_letter', function($join)
		{

			$join->on('ins_job_candidate.job_id', '=', 'ins_covering_letter.job_id');
		})
		->leftJoin('ins_supporting_doc', function($join)
		{

			$join->on('ins_job_candidate.job_id', '=', 'ins_supporting_doc.job_id');
		})
		->where('ins_job_candidate.job_id','=',$job_id)
		->select(['ins_job_candidate.candidate_id',
			'ins_jobs.id as job_id',
			'ins_jobs.jobid as jobid',
			'users.first_name',
			'users.last_name',
			'users.title',
			'ins_jobs.job_grade',
			'ins_jobs.job_title',
			'ins_agency_details.agency_name',
			'ins_cv.resume_url',
			'ins_cv.resume_name',
			'ins_cv.id as resumeID',
			'ins_covering_letter.coveringletter_url',
			'ins_covering_letter.covering_letter_name',
			'ins_covering_letter.id as coverLettID',
			'ins_supporting_doc.url as sup_url',
			'ins_supporting_doc.name as sup_name',
			'ins_supporting_doc.id as sup_id'])->orderBy('users.id','asc')->get();
	}

	public function getCandidatesSceduled($job_id) {

		$can_det = DB::table('interview_pending_candidate')->where('job_id','=',$job_id)->select('candidate_id')
		->orderBy('candidate_id','asc')->get();
		if (!empty($can_det)) {
			return $can_det;
		} else {
			return 0;
		}
	}

	public function getJobDetails($job_id) {
		return  DB::table('ins_jobs')->where('ins_jobs.id','=',$job_id)->leftJoin('ins_agency_details', function($join)
		{
			$join->on('ins_agency_details.id', '=', 'ins_jobs.agency_id');
		})->select(['ins_jobs.*','ins_agency_details.agency_name'])->where('ins_agency_details.is_active' ,'1')->first();
	}

	public function postScheduleInterview($job_id,$timings,$minutes) {
		DB::table('ins_interview_pending_dates')->where('job_id','=',$job_id)->delete();
		foreach($timings as $timing) {
			$schedule_interview = new ScheduleInterview();
			$schedule_interview->job_id = $job_id;
			$schedule_interview->interview_dates = $timing->seldate;
			$schedule_interview->interview_timings = $timing->seltime;
			$schedule_interview->time = $minutes;
			$schedule_interview->save();
		}
		return 'true';
	}

	public function getJobID() {
		$mytime = date('Y-m-d 15:30:00');
		return DB::table('ins_jobs')
		->leftJoin('ins_job_candidate', function($join) {
			$join->on('ins_job_candidate.job_id', '=', 'ins_jobs.id');
		})
		->where('ins_job_candidate.ins_progress','=','121660000')
		->orWhere('ins_job_candidate.ins_progress','=','121660021')
		->where('application_due','<=',$mytime)
		->get();
	}

	public function getCandiateConfirmation($job_id) {
		return  DB::table('interview_pending_candidate')->where('job_id','=',$job_id)->select(['candidate_id'])->get();
	}

	public function getCandiateEmail($candidate_id) {
		return  DB::table('users')->where('users.id','=',$candidate_id)
		->join('ins_employees', 'ins_employees.user_id','=', 'users.id')
		->first();
	}

	public function getResume($candidate_id,$job_id) {
		return DB::table('ins_cv')->where('candidate_id','=',$candidate_id)
		->where('job_id','=',$job_id)->select(['id','resume_url','resume_name'])->first();
	}

	public function getCoveringLetter($candidate_id,$job_id) {
		return DB::table('ins_covering_letter')->where('candidate_id','=',$candidate_id)
		->where('job_id','=',$job_id)->select(['id','coveringletter_url','covering_letter_name'])->first();
	}

	public function getSupportingDoc($candidate_id,$job_id) {
		return DB::table('ins_supporting_doc')->where('candidate_id','=',$candidate_id)
		->where('job_id','=',$job_id)->select(['id','url','name'])->first();
	}
}
