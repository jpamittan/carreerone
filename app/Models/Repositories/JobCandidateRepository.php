<?php

namespace App\Models\Repositories;

use Illuminate\Support\Facades\Auth;
use App\Models\Entities\JobCandidateID;

class JobCandidateRepository {
	function saveJobCandidate($job_id) {
		$userID = Auth::id();
		$jobCandidate = JobCandidateID::where('job_id' , '=', $job_id)
		->where('candidate_id' , '=', $userID)
		->first();
		if(!$jobCandidate) {
			$jobCandidate = new JobCandidateID();
			$jobCandidate->job_id = $job_id;
			$jobCandidate->candidate_id = $userID;
		} else {
			$jobCandidate->updated_at = date('Y-m-d H:i:s');
		}
        $jobCandidate->save();
		return $jobCandidate;
	}
}
