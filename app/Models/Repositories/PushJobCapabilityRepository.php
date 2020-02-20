<?php

namespace App\Models\Repositories;

use App\Models\Entities\JobCandidateID;
use DB;

class PushJobCapabilityRepository extends RepositoryBase {
	public function getJobCapabilityIDs() {
		$job_ids = DB::table('ins_capability_job')->select("job_id")->groupBy("job_id")->get();
		return $job_ids;
	}

	public function getJobID($job_id) {
		$jobid = DB::table('ins_jobs')->where('id','=', $job_id)->select(['jobid'])->groupBy("jobid")->first();
		return $jobid;
	}
	
	public function getJobCapability($job_id) {
		$jobApplied = DB::table('ins_capability_job')
		->leftJoin('ins_capability_match_names', function($join) {
			$join->on('ins_capability_job.capability_name_id', '=', 'ins_capability_match_names.id');
		})->where("job_id", $job_id)
		->select(['ins_capability_job.*', 'ins_capability_match_names.crm_match_names', 'ins_capability_match_names.crm_match_core_status'])
		->get();
		return $jobApplied;
	}
}
