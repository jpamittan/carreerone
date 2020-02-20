<?php
namespace App\Models\Services;

use App\Models\Entities\Jobs;
use App\Models\Entities\Resumes;
use App\Models\Gateways\Redact\JobTitleSkillMatchJojari;
use Illuminate\Support\Facades\Config;
 
class JobTitleSkillMatch {
	private $url;
	private $username;
	private $password;

	public function start($data) {
		$type= $data['type'];
		$skillmatch =new JobTitleSkillMatchJojari($type);
        $res = $skillmatch->clean($data);
        print_r($res);exit;
	}

    /**
     * //@toDo: Check if active in the command
     * This function is used to skill the new jobs added for a given candidate
     *
     * @param $jobId
     */
    public function skillMatchJobForCandidate($jobId) {
        try {
            // @ToDo: Fix the repository to load the Job only once. There are too may functions loading the same things with different fields
            // Lets check if the JOB is active and
            $job = Jobs::find($jobId);
            if (!empty($job)) {
                $jobRepository = app('App\Models\Repositories\JobRepository');
                // Lets check if we have any Job matches
                //if ($jobRepository->matchSkillJobID($jobId) == 1) {
                $skillJobIds = $jobRepository->getSkillID($jobId);
                if (!empty($skillJobIds)) {
                    foreach ($skillJobIds as $skill_job_id) {
                        $skill_id = $skill_job_id->skill_id;
                        $candidate_ids = $jobRepository->getCandidateID($jobId);
                        foreach ($candidate_ids as $candidate_id) {
                            $candidate_id = $candidate_id->candidate_id;
                            //, 'job_id' => $jobId
                            $resume_ids = Resumes::where(['candidate_id' => $candidate_id])->get();

                            // Category of the Resume or if Master
                            // =========================================================================================
                            //
                            // 1. 500 is the Master category
                            // 2. 501 is Draft
                            //
                            // @ToDo: 500 and 501 are hardcoded needs to be changed.
                            foreach ($resume_ids as $resume_id) {
                                if (($resume_id->category_id == $job->job_category_id || $resume_id->category_id == 500)
                                    && $resume_id->category_id != 501)
                                {
                                    echo "Matched [Resume ID: ".$resume_id->id."][Candidate ID: ".$candidate_id."]\n";
                                    $resume_id = $resume_id->id;
                                    $skill_match = $jobRepository->skill_match_candidate($jobId, $candidate_id, $resume_id, $skill_id);
                                    if ($skill_match != 1) {
                                        $jobRepository->postSkillMatch($jobId, $candidate_id, $resume_id, $skill_id, 0);
                                    } else {
                                        $jobRepository->postSkillMatch($jobId, $candidate_id, $resume_id, $skill_id, 1);
                                    }
                                }
                            }
                            // Load all the other skills added by the User
                            $skills = app('App\Models\Services\SkillAssessmentService')->getSkillsForCandidate($candidate_id);
                            if (!empty($skills)) {
                                foreach ($skills as $skill) {
                                    $skill_match = $jobRepository->skill_match_candidate($jobId, $candidate_id, 0, $skill->skill_asse_type_id);
                                    if ($skill_match != 1) {
                                        $jobRepository->postSkillMatch($jobId, $candidate_id, 0, $skill->skill_asse_type_id, 0);
                                    } else {
                                        $jobRepository->postSkillMatch($jobId, $candidate_id, 0, $skill->skill_asse_type_id, 1);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $exception) {

        }
    }
}