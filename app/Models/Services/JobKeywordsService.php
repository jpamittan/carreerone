<?php

namespace App\Models\Services;
use App\Models\Repositories\JobKeywordsRepository;

class JobKeywordsService {
	private $jobKeywordsRepository;
	
	function __construct(){
		$jobKeywordsRepository = new JobKeywordsRepository();
		$this->jobKeywordsRepository = $jobKeywordsRepository;
	}
	
	function saveKeywords($job_id, $keywords){
		return $this->jobKeywordsRepository->saveKeywords($job_id, $keywords);
	}
}
