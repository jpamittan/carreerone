<?php

namespace App\Models\Services;
use App\Models\Repositories\JobCandidateRepository;

class JobCandidateService {
	private $jobCandidateRepository;
	
	function __construct() {
		$jobCandidateRepository = new JobCandidateRepository();
		$this->jobCandidateRepository = $jobCandidateRepository;
	}
	
	function saveJobCandidate($job_id){
		return $this->jobCandidateRepository->saveJobCandidate($job_id);
	}
}
