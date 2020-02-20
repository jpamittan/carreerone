<?php

namespace App\Models\Repositories;
use App\Models\Entities\JobKeywords;

class JobKeywordsRepository {
	function saveKeywords($job_id, $keywords){
		$jobKeywords = JobKeywords::where('job_id' , '=', $job_id)->first();
		if(!$jobKeywords) {
			$jobKeywords = new JobKeywords();
		}
		$jobKeywords->job_id = $job_id;
		$jobKeywords->keywords = json_encode($keywords);
        $jobKeywords->save();
		return $jobKeywords;
	}
}