<?php

namespace App\Models\Repositories;

use App\Models\Entities\JobCandidateID;
use App\Models\Entities\JobMatchCandidateID;
use DB;

class CrmSyncDataRepository extends RepositoryBase {
	public function getJobAppliedUpdate(){
		$jobApplied = JobMatchCandidateID::where("ins_pushed", 'N')->get();
		return $jobApplied;
	}

	public function getJobDet($job_id){
	   $jobs = DB::table('ins_jobs')
		   ->where('id', '=', $job_id)
		   ->select('ins_jobs.*')
		   ->first() ;
	   	return $jobs;
	}

	public function getInterviewConfirmationJobId(){
		$jobid = \DB::table('ins_jobs')->where('id', 67)->first();
		return $jobid;
	}
}
