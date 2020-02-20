<?php

namespace App\Models\Services;

use App\Libraries\Sanitize;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Containers\DataObject;
use App\Models\Containers\ResumeExtract;
use App\Models\Factories\ExternalFileFactory;
use App\Models\Gateways\RedactGateway;
use App\Models\Gateways\ICS;
use App\Models\Gateways\Redact\ResumeRedact;
use App\Models\Entities\User;
use App\Models\Entities\SkillMatchNames;
use App\Models\Entities\CandidateSkillMatch;
use App\Models\Entities\RuntimeConfig;
use App\Models\Entities\Resumes;
use App\Models\Entities\JobMatchCandidate;
use App\Models\Entities\CategoryNames;
use App\Models\Entities\JobMatchCandidateCategory;
use App\Models\Proxies\FileProxy;
use App\Models\Services\BlacklistedWordsService;
use App\Models\Services\JobKeywordsService;
use App\Models\Services\ResumeService;
use App\Models\Services\PushJobmatchStatus;
use App\Models\Repositories\EmailRepository;
use App\Models\Repositories\CapabilityMatchRepository;
use App\Models\Repositories\RepositoryBase;
use Carbon\Carbon;
use DB, Mail, Config, Redirect, Session, URL, Validator, View, Format;

class JobAssetService {
  /**
   * Response object with details
   */
  private $response = null;

  /**
   * Response object with details
   */
  private $repo = null;

  /**
   * ALl class dependencies in the constructor
   */
  public function __construct() {
      $this->repo = app()->make('App\Models\Repositories\JobRepository');
  }

  /**
  * This function returns all the potentially matched jobs for the User logged in.
  *
  * @param bool $isExpired
  * @param int $offset
  * @param int $limit
  * @param null $userId
  *
  * @return mixed
  */
  public function getMatchedJobHistory($isExpired = false, $offset = 0, $limit = 20, $userId = null) {
    // The primary query used to return the Jobs.
    $primaryQuery = DB::table('ins_jobs')->select('ins_jobs.*', 'ins_jobmatch.*','ins_jobmatch.match_status as jobmatchstatus', 'ins_agency_details.*', 'ins_job_category.*')
    ->leftJoin('ins_jobmatch', function ($join) {
      $join->on('ins_jobs.id', '=', 'ins_jobmatch.job_id');
    })
    ->leftJoin('ins_agency_details', function ($join) {
      $join->on('ins_agency_details.id', '=', 'ins_jobs.agency_id');
    })
    ->leftJoin('ins_job_category', function ($join) {
      $join->on('ins_job_category.id', '=', 'ins_jobs.job_category_id');
    });
    // Lets check the jobs we are load require to be expired or not!
    if($isExpired) {
      $primaryQuery->where(function ($query) {
        $query->Where('ins_jobs.deadline_date', '<', date('Y-m-d 15:00:00'));
        $query->whereIn('match_status', array_merge(config('ins.job_match_statuses_expired'), config('ins.potential_match_statuses_not_expired')));
      });
      $primaryQuery->orderBy('ins_jobs.deadline_date', 'DESC');
    } else {
      $primaryQuery->whereIn('match_status', config('ins.potential_match_statuses_not_expired'));
      $primaryQuery->where(function ($query) {
        $query->whereNull('ins_jobs.is_expired');
        $query->orWhere('ins_jobs.deadline_date', '>=', Carbon::now());
      });
      $primaryQuery->orderBy('ins_jobs.deadline_date', 'ASC');
    }
    // If we have a overriding UserId use that otherwise just use the person who is logged in
    if(empty($userId)) {
      $userId = Auth::user()->id;
    }
    $primaryQuery->where('ins_jobmatch.candidate_id', '=', $userId);
    // Lets apply the pagination variables, and return the query data.
    return $primaryQuery->skip($offset * $limit)->take($limit)->get();
  }

  public function JobMatchCandidate() {
    $job_ids = $this->repo->getJobID();
    foreach($job_ids as $job) {
      $job_id = $job->id;
      $salary_package = $job->salary_package;
      $candidate_ids = DB::table('ins_job_candidate')->where('job_id', '=', $job_id)->select(['candidate_id'])->get();
      foreach($candidate_ids as $candidateID) {
        $candidate_id =  $candidateID->candidate_id;
        $salary_candidate = $this->repo->getCandidateSlaryFrom($job_id,$candidate_id);
        $salary_job = $this->repo->getJobSlaryFrom($job_id);
        $job_salary_from = $salary_job->salary_from;
        $job_salary_to = $salary_job->salary_to;
        if(!empty($salary_candidate)){
          //cal 5% more and less of 'salary_from' From candidate
          $percentage_from =(5/100)* $salary_candidate->salary_from;
          $salary_from_candidate_more = $salary_candidate->salary_from + $percentage_from;
          $salary_from_candidate_less = $salary_candidate->salary_from - $percentage_from;
          //cal 5% more and less of 'salary_to' From candidate
          $percentage_to =(5/100)* $salary_candidate->salary_to;
          $salary_to_candidate_more = $salary_candidate->salary_to + $percentage_to;
          $salary_to_candidate_less = $salary_candidate->salary_to - $percentage_to;
          if($salary_candidate->salary_from == $job_salary_from || $salary_from_candidate_more >= $job_salary_from || $salary_from_candidate_more <= $job_salary_from){
            if($salary_candidate->salary_to == $job_salary_to || $salary_to_candidate_more >= $job_salary_to || $salary_to_candidate_more <= $job_salary_to || $salary_candidate->salary_to == 0){
              $this->repo->postJobMatchStatus1($job_id,$candidate_id);
            }
          } else {
            $this->repo->postJobMatchStatus0($job_id,$candidate_id);
          }
        }
        $candidate_categories = $this->repo->getCandidateCategory($candidate_id,$job_id);
        $status = 0;
        if(!isset($candidate_categories)){
          foreach($candidate_categories as $candidate_category){
            $cand_categ_id = $candidate_category->category_id;
            if($cand_categ_id == $salary_job->job_category_id){
              $status = 1;
              break;
            }
          }
        }
        DB::table('ins_jobmatch')->where('candidate_id','=', $candidate_id)->update(['category_status'=> $status]);
      }
    }
  }

  public function getAllJobs() {
    $jobs = $this->repo->getAllJobs();
    return $jobs;
  }

  public function getJobDetails($params= array(), $page =1, $limit =20, $type='ins', $notinmatchids=array()) {
    $jobs = $this->repo->getJobDetails($params, $page  , $limit , $type, $notinmatchids);
    return $jobs;
  }

  public function getHistoryJobs($params= array(), $page =1, $limit =20, $type='ins', $notinmatchids=array()) {
    $jobs = $this->repo->getHistoryJobs($params, $page  , $limit , $type, $notinmatchids);
    return $jobs;
  }

  public function getMatchedJobs($params= array(), $page =1, $limit =20, $type='matched', $notinmatchids=array()) {
    $jobs = $this->repo->getMatchedJobs($params, $page  , $limit , $type, $notinmatchids);
    return $jobs;
  }

  public function getFutureJobs($params= array(), $page =1, $limit =20, $type='matched', $notinmatchids=array()) {
    $jobs = $this->repo->getFutureJobs($params, $page  , $limit , $type, $notinmatchids);
    return $jobs;
  }

  public function insertRssFeeds($item,$image) {
    $this->repo->insertRssFeeds($item,$image);
    return 'true';
  }

  public function getRssFeeds() {
    return $this->repo->getRssFeeds();
  }

  public function getProfileDetails() {
    $typeid = $this->repo->getUserTypeID();
    $type = $typeid->type;
    if($type == 'EiT'){
      return  $this->repo->getEmplyDetails();
    } else if($type == 'Individual'){
      return  $this->repo->getClientDetails();
    }
  }

  public function getSkillassement() {
    $skill = $this->repo->getSkillassement();
    return $skill;
  }

  public function getSkillAssessments() {
    $skills = [];
    foreach ($this->repo->getSkillAssessments(Auth::id()) as $skill) {
      if(!isset($skills[$skill->skill_group_name])) {
        $skills[$skill->skill_group_name] = [];
      }
      $skills[$skill->skill_group_name][] = $skill;
    }
    return $skills;
  }

  public function getSkillGroupList() {
    return $this->repo->getSkillGroupList();
  }

  public function getSkillList() {
    return $this->repo->getSkillList(Auth::id());
  }

  public function getLocation() {
    return $this->repo->getLocation();
  }

  /**
   * @return mixed|array
   */
  public function getAllAvailableCategories() {
    return $this->repo->getAllAvailableCategories();
  }

  public function getCategory() {
    return $this->repo->getCategory();
  }

  public function getUserLocation() {
    return $this->repo->getUserLocation();
  }

  public function getUserResume() {
    return $this->repo->getUserResume();
  }

  public function getUserCapbsPDF() {
    return  $this->repo->getUserCapbsPDF();
  }

  public function getUserCategory() {
    return  $this->repo->getUserCategory();
  }

  public function getJobsone() {
    $jobs = $this->repo->getJobsone();
    return $jobs;
  }

  public function getJobstwo() {
    $jobs = $this->repo->getJobstwo();
    return $jobs;
  }

  public function getJobsPosted() {
    $jobs = $this->repo->getJobsPosted();
    return $jobs;
  }

  public function getJobsExpiring() {
    $jobs = $this->repo->getJobsExpiring();
    return $jobs;
  }

  public function getJob($job_id) {
    $job = $this->repo->getjob($job_id);
    return $job;
  }

  public function insertJobEoi($job_id ,$status ,$ins_job_apply_id ,$comments = NULL) {
    $job = $this->repo->insertJobEoi($job_id, $status, $ins_job_apply_id, $comments);
    return $job;
  }

  public function updateJobEoi($id,  $status) {
    $job = $this->repo->updateJobEoi($id, $status);
    return $job;
  }

  public function isjobapplied($job_id) {
    $job = $this->repo->isjobapplied($job_id);
    return $job;
  }

  public function isjobeoirejected($job_id) {
    $job = $this->repo->isjobeoirejected($job_id);
    return $job;
  }

  public function isjobeoiapply($job_id) {
    $job = $this->repo->isjobeoiapply($job_id);
    return $job;
  }

  public function isjobeoi($job_id) {
    $job = $this->repo->isjobeoi($job_id);
    return $job;
  }

  public function isjobdraft($job_id) {
    $job = $this->repo->isjobdraft($job_id);
    return $job;
  }

  public function jobProgressStatus($job_id) {
    $job = $this->repo->jobProgressStatus($job_id);
    return $job;
  }

  public function isjobfromdraft($job_id) {
    $job = $this->repo->isjobfromdraft($job_id);
    return $job;
  }

  public function rejectJob($job_id) {
    $this->repo->rejectJob($job_id);
    $job_det =  $this->repo->getJob($job_id);
    $typeid = $this->repo->getUserTypeID();
    $type = $typeid->type;
    if($type == 'EiT'){
      $user_det = $this->repo->getEmplyDetails();
      $jobmatch_id = $this->repo->getJobMatchId($job_id);
      if(!empty($jobmatch_id)){
        $jobmatch_id1 =  $this->repo->updateJobMatchStatus($jobmatch_id->new_jobmatchedid,$status=121660004);
        $jobmatchstatus = new PushJobmatchStatus();
        $jobmatchstatus->pushJobMatchStatus($jobmatch_id->new_jobmatchedid,$status=121660004);
        $this->sendCaseManagerEmail($user_det,$job_det);
      }//if
    } else if ($type == 'Individual'){
      $user_det = $this->repo->getClientDetails();
      $jobmatch_id =  $this->repo->getJobMatchId($job_det->id);
      if(!empty($jobmatch_id)){
        $jobmatchstatus = new PushJobmatchStatus();
        $jobmatchstatus->pushJobMatchStatus($jobmatch_id->new_jobmatchedid,$status=121660004);
        $this->sendCaseManagerEmail($user_det,$job_det);
      }
    }
  }

  public function sendCaseManagerEmail($user_det,$job_det) {
    $user_det->first_name = $user_det->new_firstname;
    $user_det->last_name = $user_det->new_surname;
    $email = $this->repo->getcasemanagerEmail($user_det->ownerid);
    $emailService = new EmailRepository();
    $message = View::make('site/email/candidate-casemanager',array('user_det' => $user_det,'job_det' => $job_det))->render();
    $subject = $job_det->job_title . 'Rejected';
    $to = $email->internalemailaddress;
    $from = Config::get('ins_emails.casemanager_email.from');
    $emailService->send($message,$subject,$from,$to);
  }

  public function getJobstatus() {
    $job_ids = $this->repo->getJobstatus();
    return $job_ids;
  }

  public function applyHistory() {
    $apply_history = $this->repo->applyHistory();
    return $apply_history;
  }

  public function getResume() {
    $get_resume = $this->repo->getResume();
    return $get_resume;
  }

  public function SkillMatchCandidate() {
    $skill_match_delete = $this->repo->deleteSkillMach();
    $job_ids = $this->repo->getSkillJobID();
    foreach($job_ids as $job_id){
      $job_id = $job_id->job_id;
      $match_job_id = $this->repo->matchSkillJobID($job_id);
      if($match_job_id ==1){
        $skill_job_ids = $this->repo->getSkillID($job_id);
        foreach($skill_job_ids as $skill_job_id){
          $skill_id = $skill_job_id->skill_id;
          $candidate_ids = $this->repo->getCandidateID($job_id);
          foreach($candidate_ids as $candidate_id){
            $candidate_id = $candidate_id->candidate_id;
            $resume_ids = $this->repo->getSkillResumeID($candidate_id,$job_id);
            foreach($resume_ids as $resume_id){
              $resume_id =$resume_id->id;
              $skill_match = $this->repo->skill_match_candidate($job_id,$candidate_id,$resume_id,$skill_id);
              if($skill_match != 1){
                $this->repo->postSkillMatch($job_id,$candidate_id,$resume_id,$skill_id,$status=0);
              } else {
                $this->repo->postSkillMatch($job_id,$candidate_id,$resume_id,$skill_id,$status=1);
              }
            }
          }
        }
      } else {
        $this->skillMatchJandC($job_id);
      }
    }
  }

  public function skillMatchJandC($job_id) {
    $skill_job_ids = $this->repo->getSkillID($job_id);
    foreach($skill_job_ids as $skill_job_id){
      $skill_id = $skill_job_id->skill_id;
      $candidate_ids = $this->repo->getUserCandidateID();
      foreach($candidate_ids as $candidate_id){
        $candidate_id = $candidate_id->user_id;
        $resume_id = $this->repo->getUserResumeID($candidate_id);
        if(!empty($resume_id)){
          $resume_id = $resume_id->id;
          $skill_match = $this->repo->skill_match_candidate($job_id,$candidate_id,$resume_id,$skill_id);
          if($skill_match != 1){
            $this->repo->postSkillMatch($job_id,$candidate_id,$resume_id,$skill_id,$status=0);
          } else {
            $this->repo->postSkillMatch($job_id,$candidate_id,$resume_id,$skill_id,$status=1);
          }
        }
      }
    }
  }

  public function getskills($job_id, $resumeId = null) {
    $userID = Auth::id();
    DB::table('ins_skillmatch')->where('candidate_id','=',$userID)->where('job_id','=',$job_id)->delete();
    $job_skill =  array();
    $cand_skill =  array();
    $jobskills = $this->repo->getskillByJobID($job_id, true);
    $candidateskills = $this->repo->getskillByCandidate($resumeId);
    if(!empty($jobskills) && !empty($candidateskills)){
      foreach($jobskills as $jobskill){
        $job_skill[$jobskill->skill_id] = 1;
      }
      foreach($candidateskills as $candidateskill){
        $cand_skill[$candidateskill->skill_id] = 1;
      }
      foreach($job_skill as $skilljob => $value){
        if(isset($cand_skill[$skilljob])){
          $this->repo->insertSkillMatch($userID, $skilljob, $job_id, 1);
        } else {
          $this->repo->insertSkillMatch($userID, $skilljob, $job_id, 0);
        }
      }
    }
    return $this->repo->getSkillMatchCandidate($job_id);
  }

  public function checkMonsterSkills($jobId, $resume) {
    $jobSkills = $this->repo->getSkillNamesByJob($jobId);
    $job = $this->repo->getjob($jobId);
    $skills = [];
    $matches = [];
    foreach ($jobSkills as $skill) {
      $name = strtolower($skill->skill_name);
      $skills[$name] = $name;
    }
    $query = 'http://prdbx.monster.com/query.ashx?rb=11517&ver=2.0&cat=1:EAAQPTJhLpvm89qNHnFvt5fRdictiOqMa1GmInHf9HUd7xaVGgJdIwbKclFeu.wrSHJlp4qfVAUviFlQgmH._JpXdRR58XuWic0cWGYlp5oUHR3iKfoiwURIPvEw_inXtIhD&rv=' . $resume->uploaded_to_monster . '&sk=' . str_replace('+', '%2520', urlencode(implode(',', $skills)));
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $query);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    $xml = simplexml_load_string($output);
    if(!isset($xml->Resumes->Resume->Skills->Skill)) {
      return false;
    }
    foreach ($xml->Resumes->Resume->Skills->Skill as $skill) {
      if(isset($skill->Matches->Label)) {
        foreach ($skill->Matches->Label as $match) {
          $match = (string)$match;
          $matches[$match] = $match;
          $match = strtolower($match);
          unset($skills[$match]);
        }
      }
    }
    $userID = Auth::id();
    DB::table('ins_skillmatch')
        ->where('candidate_id', '=', $userID)
        ->where('job_id', '=', $jobId)
        ->delete();
    foreach ($matches as $match) {
      $skill = DB::table('ins_skillmatch_names')->where('skill_name', '=', $skill)->first();
      if (! $skill) {
        $skill = new SkillMatchNames();
        $skill->skill_name =  $match;
        $skill->count =  0;
        $skill->save();
      }
      $this->repo->insertSkillMatch($userID, $skill->id, $jobId, 1);
    }
    foreach ($skills as $mismatch) {
      $skill = DB::table('ins_skillmatch_names')->where('skill_name', '=', $mismatch)->first();
      if (!$skill) {
        $skill = new SkillMatchNames();
        $skill->skill_name =  $mismatch;
        $skill->count =  0;
        $skill->save();
      }
      $this->repo->insertSkillMatch($userID, $skill->id, $jobId, 0);
    }
    return $this->repo->getSkillMatchCandidate($jobId);
  }

  public function getCapabilities($job_id){
    $getcapabilities = $this->repo->getCapabilities($job_id);
    return $getcapabilities;
  }

  public function getCandidateInterview(){
    $get_candidate_interviews = $this->repo->getCandidateInterviews();
    // Remove interview expired dates
    foreach ($get_candidate_interviews as $key => $value) {
    if(date($value->interview_date) < date('Y-m-d H:i:s'))
      unset($get_candidate_interviews[$key]);
    }
    return $get_candidate_interviews;
  }

  public function getPendingCandidateInterview(){
    return $this->repo->getPendingCandidateInterview();
  }

  public function getScheduleCandidateInterview(){
    $get_Schedule_Candidate_Interview = $this->repo->getScheduleCandidateInterview();
    // Remove interview expired dates
    foreach ($get_Schedule_Candidate_Interview as $key => $value) {
    if(date($value->interview_date) < date('Y-m-d H:i:s'))
        unset($get_Schedule_Candidate_Interview[$key]);
    }
    return  $get_Schedule_Candidate_Interview;
  }

  public function getCompletedCandidateInterview(){
    return $this->repo->getCompletedCandidateInterview();
  }

  public function getCandidateInterviewDates(){
    $get_candidate_interviews_dates = $this->repo->getCandidateInterviewDates();
    return $get_candidate_interviews_dates;
  }

  public function deleteCandidateJob($job_id) {
    $delete_job = $this->repo->deleteCandidateJob($job_id);
    return $delete_job;
  }

  public function getCandidatePendingDates() {
    return $this->repo->getCandidatePendingDates();
  }

  public function candidateCalandarPendingDates($id) {
    $dates= $this->repo->getPendingDate($id);
    if(!empty($dates)){
      $job_id = $dates->job_id;
      $date =  $dates->interview_dates;
      $get_candidate_pending_interview  = $this->repo->getPendingInterview($id,$job_id);
      return $get_candidate_pending_interview;
    } else {
      return 'null';
    }
  }

  public function getInterviewTimings($interview_confirmation) {
    if($interview_confirmation != 'null'){
      $timings=array();
      foreach($interview_confirmation as $interview) {
        $date = $interview->interview_dates;
        $job_id =  $interview->job_id;
        $timings = array_merge($timings ,$this->repo->getInterviewTimings($job_id,$date));
      }
      return $timings;
    }
  }

  public function postAcceptInterview($input) {
    $id = $input['interview_id'];
    $details = $this->repo->getPendingInterJobID($id);
    $job_id = $details->job_id;
    $timings = $details->interview_timings;
    $date =$details->interview_dates;
    $chk_details = $this->repo->chkInterviewCalandarDetails($timings,$date);
    if($chk_details != 1){
      $ins_job_candidate_id = $this->repo->getInterviewPendingCandidate($job_id);
      $comments = '';
      $this->repo->deleteInterviewPendingDates($job_id,$timings);
      $this->repo->deleteInterviewPendingCandidate($job_id);
      $id = $this->repo->insertInterviewConfirmation($details,$status = 1,$comment = NULL);
      $interview_det = $this->repo->getInterviewDet($id);
      $job_det = $this->repo->getjob($interview_det->job_id);
      $user_det = $this->repo->getUserInfo($interview_det->candidate_id);
      //PUSH to CRM
      foreach($ins_job_candidate_id as  $ins){
        $ins_job_candidate_id = $ins->ins_job_candidate_id;
        $res =   DB::table('ins_job_candidate')->where('id', '=',$ins_job_candidate_id)->first();
        $ins_job_apply_id = $res->ins_job_apply_id;
        if(!empty($ins_job_apply_id)){
          $comments = $res->comments;
          $comments_panelmember = $res->panel_member;
          $this->sendEmailConfirmation($interview_det,$job_det,$user_det,$comments,$comments_panelmember);
          $this->sendRecruiterCandConfirmation($interview_det,$job_det,$user_det,$comments,$comments_panelmember);
          $details = "";
          $details .= "Date :  " . $interview_det->interview_date;
          $details .= "\r";
          $details .= "Time : " . $interview_det->interview_time ;
          $details .= "\r";
          $details .= $user_det->first_name." " .  $user_det->last_name;
          $details .= "\r";
          $details .= "Role Title: " . $job_det->job_title;
          $details .= "\r";
          $details .= "AgencyName: " . $job_det->agency_name;
          $details .= "\r";
          $details .= "Grade: " . $job_det->job_grade;
          $details .= "\r";
          $int_time =explode("-", $interview_det->interview_time);
          $data['ins_job_apply_id'] = $ins_job_apply_id ;
          $data['interviewconfirmed'] =1 ;
          $time ='';
          if(!empty($int_time[0])){
            $time = "T" . @$int_time[0].":00";
          }
          $data['interviewdate'] = $interview_det->interview_date . $time;
          $data['interviewdetails'] =  $details ;
          $data['comments'] = $comments ;
          $data['panel_member'] = $comments_panelmember;
          $service = app()->make('App\Models\Services\CrmSyncDataService');
          $service->updateInterviewConfirmation($data);
        }
      }
      return 'true';
    } else {
      return 'false';
    }
  }

  public function sendEmailConfirmation($interview_det, $job_det, $user_det, $comments = '', $comments_panelmember = '') {
    $emailService = new EmailRepository();
    $message = View::make('site/email/confirm-interview',array(
        'interview_det' => $interview_det, 'job_det' => $job_det, 'user_det' => $user_det,
        'comments' => $comments, 'comments_panelmember' => $comments_panelmember,
        'case_manager' => !empty($user_det) && !empty($user_det->employee) && !empty($user_det->employee->caseManager) ? $user_det->employee->caseManager : null
      ))->render();
    $subject = 'Interview Confirmed For the Role - '.$job_det->job_title;
    $to = $user_det->email;
    $from = Config::get('ins_emails.confirm_interview.from');
    $invite = new ICS();
    $i_time = explode("-", $interview_det->interview_time);
    $start = $interview_det->interview_date ." ". @$i_time[0];
    $end = $interview_det->interview_date ." " .@$i_time[1];
    $invite->setFilename("interview_".$user_det->first_name."_".$user_det->last_name)
          ->setSubject($subject)
          ->setDescription($comments . $comments_panelmember )
          ->setStart($start )
          ->setEnd( $end)
          ->setLocation("Sydney");
    $path = $invite->save();
    $attachments = array($path);
    $emailService->sendAttachment( $to , $from , $subject , '' ,  $message , $attachments);
  }

  public function sendRecruiterCandConfirmation($interview_det, $job_det, $user_det, $comments = '', $comments_panelmember = '') {
    $emailService = new EmailRepository();
    $message = View::make('site/email/confirm-recruiter-cand-interview',array('interview_det' => $interview_det,'job_det' => $job_det,'user_det'=>$user_det ,'comments'=>$comments ,'comments_panelmember'=>$comments_panelmember))->render();;
    $subject = 'Interview Confirmed for The Candidate - '.$user_det->first_name.' '.$user_det->last_name;
    $to = $job_det->prepared_by_email;
    $cc = '';
    $recruiter_email_override = DB::table('runtime_config')->where('name', '=', 'recruiter_email_override')->first();
    if(!empty($recruiter_email_override->value)){
      $recruiter_email_override->value = filter_var($recruiter_email_override->value, FILTER_SANITIZE_EMAIL);
      if(filter_var($recruiter_email_override->value, FILTER_VALIDATE_EMAIL)){
        $to = $recruiter_email_override->value;
      }
    } else {
      $cc = 'ava@inscm.com.au';
      $case_manager_email = $this->repo->getcasemanagerEmail($user_det->crm_user_id);
      if(filter_var($case_manager_email->internalemailaddress, FILTER_VALIDATE_EMAIL)){
        $cc .= ",".$case_manager_email->internalemailaddress;
      }
    }
    $from = Config::get('ins_emails.recruiter_confirmation.from');
    $invite = new ICS();
    $i_time = explode("-", $interview_det->interview_time);
    $start = $interview_det->interview_date ." " .@$i_time[0];
    $end = $interview_det->interview_date ." ". @$i_time[1];
    $invite->setFilename("interview_".$user_det->first_name."_".$user_det->last_name)
          ->setSubject($subject)
          ->setDescription($comments . $comments_panelmember)
          ->setStart($start )
          ->setEnd( $end)
          ->setLocation("Sydney");
    $path = $invite->save();
    $attachments = array($path);
    $emailService->sendAttachment($to, $from, $subject, '', $message, $attachments, '', $cc);
  }

  public function sendCaseManagerEmailInterviewConfirmation($interview_det, $job_det, $user_det, $comments = '') {
    $email = $this->repo->getcasemanagerEmail($user_det->ownerid);
    $emailService = new EmailRepository();
    $message = View::make('site/email/confirm-casemanager-cand-interview',array('interview_det' => $interview_det,'job_det' => $job_det,'user_det'=>$user_det ,'comments'=>$comments , 'email' => $email ));
    $subject = 'Interview Confirmed for The Candidate - '.$user_det->first_name.' '.$user_det->last_name;
    $to = $email->internalemailaddress;
    $from = Config::get('ins_emails.casemanager_email.from');
    $emailService->send($message,$subject,$from,$to);
  }

  public function sendEmailReject($interview_det, $job_det, $user_det) {
    $emailService = new EmailRepository();
    $message = View::make('site/email/reject-interview',array(
        'interview_det' => $interview_det, 'job_det' => $job_det, 'user_det' => $user_det,
        'case_manager' => !empty($user_det->employee) && !empty($user_det->employee->caseManager) ? $user_det->employee->caseManager : null
    ));
    $subject = 'Interview Rejected For the Role - '.$job_det->job_title;
    $to = $user_det->email;
    $from = Config::get('ins_emails.confirm_interview.from');
    $emailService->send($message,$subject,$from,$to);
  }

  public function sendRecruiterCandReject($interview_det, $job_det, $user_det, $comment) {
    $emailService = new EmailRepository();
    $message = View::make('site/email/reject-recruiter-cand-interview',array('interview_det' => $interview_det,'job_det' => $job_det,'user_det'=>$user_det , 'comment'=> $comment));
    $subject = 'Interview Rejected for The Candidate - '.$user_det->first_name.' '.$user_det->last_name;
    $to = $user_det->email;
    $from = Config::get('ins_emails.recruiter_confirmation.from');
    $emailService->send($message,$subject,$from,$to);
  }

  public function postRejectInterview($input) {
    $comment = $input['comment'];
    $id = $input['interview_pending_date_id'];
    $details = $this->repo->getPendingInterJobID($id);
    $job_id = $details->job_id;
    $timings = $details->interview_timings;
    $date =$details->interview_dates;
    $this->repo->deleteInterviewPendingDates($job_id,$timings);
    $this->repo->deleteInterviewPendingCandidate($job_id);
    $id =  $this->repo->insertInterviewConfirmation($details,$status = 2,$comment);
    $ins_job_candidate_id = $this->repo->getInterviewPendingCandidate($job_id);
    //PUSH to CRM
    foreach($ins_job_candidate_id as  $ins){
      $ins_job_candidate_id = $ins->ins_job_candidate_id;
      $res =   DB::table('ins_job_candidate')->where('id', '=',$ins_job_candidate_id)->first();
        if(!empty($res)){
          $ins_job_apply_id = $res->ins_job_apply_id;
          DB::table('ins_job_candidate')->where('id', '=',$ins_job_candidate_id)->update(['ins_progress' => 121660013]);
          //: Declined/withdrawn – Accepted another role
          $interview_det = $this->repo->getInterviewDet($id);
          $job_det = $this->repo->getjob($interview_det->job_id);
          $user_det = $this->repo->getUserInfo($interview_det->candidate_id);
          $this->sendEmailReject($interview_det,$job_det,$user_det);
          $this->sendRecruiterCandReject($interview_det,$job_det,$user_det , $comment);
          $data['ins_job_apply_id'] = $ins_job_apply_id ;
          $service = app()->make('App\Models\Services\UpdateJobApplied');
          $service->updateCRMJobAppliedINSProgress($ins_job_apply_id  , 121660013);
        }

    }
    return true;
  }

  public function getCandidateInterviewJobDetails($id) {
    $job_id = $this->repo->getPendingInterJobID($id);
    if(isset($job_id) || !empty($job_id)){
      $job_id =$job_id->job_id;
      $job_detail['job_details'] =  $this->repo->getCandidateInterviewJobdetails($job_id);
      $userID = Auth::id();
      $job_candidate = DB::table('ins_job_candidate')->where('job_id', '=', $job_id)->where('candidate_id', '=', $userID)->first();
      $job_detail['job_candidate'] = $job_candidate;
      return $job_detail;
    } else {
      return "false";
    }
  }

  /**
   * This function loads a description and removes all the backlisted words from it and then returns an array with
   * the keywords left and there count.
   *
   * @param $desc
   * @return array
   */
  public function countKeywords($desc) {
    try {
      // Lets remove all the special characters
      // Change the whole string to lower case; makes it easier for comparison
      $desc = strtolower($desc);
      // Remove all the emails
      $desc = preg_replace("/[^@\s]*@[^@\s]*\.[^@\s]*/", " ", $desc);
      // Remove all the links
      $desc = preg_replace("/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i", " ", $desc);
      // Remove any special characters
      $desc = preg_replace('/[^A-Za-z0-9\-]/', ' ', $desc);
      // Remove all the digits
      $desc = preg_replace('/\d+/u', '', $desc);
      // Remove all the hyphens
      $desc = str_replace('-', ' ', $desc);
      // Convert the paragraph now into an Array
      $wordsArr = explode(' ', $desc);
      // Lets remove all the redundant words from the array
      $wordsArr = array_unique($wordsArr); //Remove duplicate words
      // Load all the
      $service = new BlacklistedWordsService();
      $BlacklistedWordsArr = $service->getAllWords(); //Fetch blaclisted words from databas
      // Flip the array so the value become keys. Run a diff against available blacklisted words so we can remove any
      // things that is blacklisted.
      $wordsArr = array_flip(array_diff_key(array_flip($wordsArr), array_flip($BlacklistedWordsArr)));
      // Loop through the words in the found keywords so we can do a count on the words.
      $wordCountArr = array();
      foreach($wordsArr as $value) {
        if(!empty($value)) {
          $wordCountArr[$value] = substr_count($desc, $value);
        }
      }
      // Sort by the count
      natsort($wordCountArr);
      // Get the higher count on the top
      $wordCountArr = array_reverse($wordCountArr);
      // If it is more than 10 remove only take the first 10
      if(sizeof($wordCountArr) >= 10) {
        $wordCountArr = array_slice($wordCountArr, 0, 10); //Get top 10
      }
      // Return what we have found
      return $wordCountArr;
    } catch (\Exception $exception) {
      logger('Failed to retrieve the count of words');
      logger($exception->getMessage());
    }//catch
    return [0 => 'Failed to load keywords'];
  }

  public function saveKeywords($job_id, $keywords) {
    $jobKeywordsService = new JobKeywordsService();
    $saveKeywords = $jobKeywordsService->saveKeywords($job_id, $keywords);
    return $saveKeywords;
  }

  public function saveJobCandidate($job_id) {
    $jobCandidateService = new JobCandidateService();
    $savejobCandidate = $jobCandidateService->saveJobCandidate($job_id);
    return $savejobCandidate;
  }

  public function getJobIsLatest() {
    $getJobIsLatest = $this->repo->getJobIsLatest();
    return $getJobIsLatest;
  }

  public function saveCRM($job_id) {
    $job_det =  $this->repo->getJob($job_id);
    $jobmatch_id =  $this->repo->getJobMatchId($job_det->id);
    if(!empty($jobmatch_id)){
      $jobmatchstatus = new PushJobmatchStatus();
      $jobmatchstatus->pushJobMatchStatus($jobmatch_id->new_jobmatchedid,$status=121660006);
    }
  }

  public function getCandidateJobs($userId) {
    return $this->repo->getCandidateJobs($userId);
  }

  public function getMismatches($capabilities, $jobs) {
    $matches = [];
    $repo = new CapabilityMatchRepository();
    foreach ($capabilities as $group) {
      foreach ($group['capabilities'] as &$id) {
        if ($id->criteria != 'Met') {
          continue;
        }
        $id->match = [];
        $id->mismatch = [];
        $id->mismatchGroups = [];
        $matches[$id->capability_name_id] = $id;
      }
    }
    unset($id);
    foreach($jobs as $job) {
      $jobCapabilities = $repo->getJobCapabilities($job);
      foreach ($jobCapabilities as $capability) {
        if (isset($matches[$capability->capability_name_id])) {
          if ($matches[$capability->capability_name_id]->level_id >= $capability->level_id) {
            $matches[$capability->capability_name_id]->match[] = [
              'job_id' => $job->job_id,
              'title' => $job->job_title,
              'level_name' => $capability->level_name,
              'level_id' => $capability->level_id
            ];
          } else {
            $matches[$capability->capability_name_id]->mismatch[] = [
              'job_id' => $job->job_id,
              'title' => $job->job_title,
              'level_name' => $capability->level_name,
              'level_id' => $capability->level_id
            ];
          }
        }
      }
    }
    foreach ($matches as $match) {
      foreach($match->mismatch as $mismatch) {
        if (! isset($match->mismatchGroups[$mismatch['level_id']])) {
          $match->mismatchGroups[$mismatch['level_id']] = [
            'name' => $mismatch['level_name'],
            'count' => 0
          ];
        }
        $match->mismatchGroups[$mismatch['level_id']]['count'] ++;
      }
      ksort($match->mismatchGroups);
    }
    return $capabilities;
  }

  public function uploadToMonster($resume, $user, $category) {
    $content = file_get_contents($resume->resume_url);
    $soap = View::make('site/partials/monster-resume-upload', [
            'content' => base64_encode($content),
            'mimetype' => \GuzzleHttp\Psr7\mimetype_from_extension($resume->extension),
            'filename' => $resume->resume_name,
            'modified_date' => str_replace(' ', 'T', $resume->updated_at) . 'Z',
            'user' => $user,
            'category' => $category
          ])->render();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://gateway.monster.com/bgwPower');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $soap);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/soap+xml',
    ]);
    $output = curl_exec($ch);
    $xml = simplexml_load_string($output);
    $doc = new \DOMDocument();
    $str = $xml->asXML();
    $doc->loadXML($str);
    $resumeId = null;
    foreach ($doc->getElementsByTagName('ResumeReference') as $el) {
      foreach ($el->attributes as $attr) {
        $resumeId = $attr->value;
      }
    }
    if($resumeId){
      DB::table('ins_cv')->where('id','=', $resume->id)->update(['uploaded_to_monster' => $resumeId]);
    }
  }

  public function getSkillGap($userId) {
    return $this->repo->getSkillGap($userId);
  }
}
