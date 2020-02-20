<?php
namespace App\Models\Services;

use App\Libraries\Sanitize;
use App\Models\Containers\DataObject;
use App\Models\Repositories\RepositoryBase;
use App\Models\Proxies\FileProxy;
use App\Models\Entities\User;
use App\Models\Entities\InterviewConfirmation;
use App\Models\Entities\RuntimeConfig;
use App\Models\Entities\JobCandidateID;
use App\Models\Repositories\ResumeRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Config, Redirect, Session, URL, Validator, View;
use DB;
use Mail;
use File;
use Format;

class ScheduleAssetService {
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
    $this->repo = app()->make('App\Models\Repositories\ScheduleRepository');
  }

  public function getAppliedCandidates($job_id) {
    return $this->repo->getAppliedCandidates($job_id);
  }

  public function getJobAssignedDates($job_id) {
    return $this->repo->getJobAssignedDates($job_id);
  }

  public function getCandidatesSceduled($job_id) {
    return $this->repo->getCandidatesSceduled($job_id);
  }

  public function getJobDetails($job_id) {
    return $this->repo->getJobDetails($job_id);
  }

  public function postScheduleInterview($input) {
    $candidates = json_decode($input['candidates']);
    $ins_screened = json_decode($input['ins_screened']);
    $ins_ids = json_decode($input['ins_ids']);
    $minutes = $input['time_interval'];
    $job_id = $input['job_id'];
    $comments = !empty($input['comments']) ? $input['comments']  : NULL ;
    $comments_panelmember = !empty($input['comments_panelmember']) ? $input['comments_panelmember']  : NULL ;
    $counter =0 ;
    foreach($candidates as $candidate) {
      $ins_job_candidate_id = $ins_ids[$counter];
      DB::table('interview_pending_candidate')->insert(['job_id'=>$job_id,'candidate_id'=>$candidate ,'ins_job_candidate_id' => $ins_job_candidate_id]);
      DB::table('ins_job_candidate')->where('job_id','=',$job_id)->where('candidate_id','=',$candidate)->update(['scheduled' =>1, 'comments' => $comments , 'panel_member' => $comments_panelmember]);
      $counter++;
    }
    if(!empty($candidates)){ 
      $timings = json_decode($input['date_timings']);
      $this->repo->postScheduleInterview($job_id,$timings,$minutes);
      $candidates = $this->repo->getCandiateConfirmation($job_id);
      $this->candidateemail($candidates,$job_id, $input);
    }
    //Screened
    $ins_screened_cand = array();
    $counter = 0;
    foreach($ins_screened as $candidate) {
      $ins_screened_cand[$counter]['candidate'] = $candidate_i = $candidate->candiddateid;
      $ins_job_candidate_id = $candidate->insids_screened;
      $ins_screened_cand[$counter]['comment'] = $comment = $candidate->comment;
      DB::table('ins_job_candidate')->where('job_id','=',$job_id)->where('candidate_id','=',$candidate_i)->update(['scheduled' =>1, 'screened' =>1, 'comments' => $comments   ]);
      $counter++;
    }
    if(!empty($ins_screened_cand)){ 
      //send email
      $this->screencandidateemail($ins_screened_cand,$job_id );
    }
  }

  public function candidateemail($candidates,$job_id, $input = array()) {
    foreach($candidates as $candidate) {
      $emails = $this->repo->getCandiateEmail($candidate->candidate_id);
      $job_det = $this->repo->getJobDetails($job_id);
      $this->sendCandidateEmail($emails,$job_det, $input);
    }
  }

  public function sendCandidateEmail($email,$job_det , $input =array()) {
    $comments  = !empty($input['comments']) ? $this->nl2br2( $input['comments']) : ''; 
    $comments_panelmember  = !empty($input['comments_panelmember']) ? $this->nl2br2( $input['comments_panelmember']) : '';
    $emailService = new EmailRepository();
    $message = View::make('site/email/candidate-confirmation',array('email' => $email,'job_det' => $job_det,'comments' => $comments ,'comments_panelmember' => $comments_panelmember));
    $subject = 'Interview Confirmation For the Role - '.$job_det->job_title;
    $to = $email->email;
    $from = Config::get('ins_emails.candidate_confirmation.from');
    $emailService->send($message,$subject,$from,$to);
  }

  public function screencandidateemail($candidates,$job_id) {
    $allcand=array();
    foreach($candidates as $candidate){ 
      $cand = $this->repo->getCandiateEmail($candidate['candidate']);
      $cand->comment = $candidate['comment'];
      $allcand[] = $cand;
    }
    $job_det = $this->repo->getJobDetails($job_id);
    $this->sendScreenCandidateEmail($allcand,$job_det );
  }

  public function sendScreenCandidateEmail($cand,$job_det){
    $emailService = new EmailRepository();
    $message = View::make('site/email/application-screen',array('job' => $job_det,'eits' => $cand  ))->render();
    $subject = 'Interview Confirmation For the Role - '.$job_det->job_title;
    $to = Config::get('ins_emails.candidate_screened.to');
    $from = Config::get('ins_emails.candidate_screened.from');
    $emailService->send($message,$subject,$from,$to);
  }

  public function nl2br2($string) {
    $string = str_replace(array("\r\n", "\r", "\n"), "<br />", $string);
    return $string;
  }

  public function scheduleInterview() {
    $job_ids = $this->repo->getJobID();
    if(!empty($job_ids)){
      foreach($job_ids as $job_id) {
        $job_cand_detail = $this->repo->getAppliedCandidates($job_id->id);
        if(!empty($job_cand_detail)) {
          $job_candidate_confirm = JobCandidateID::find($job_cand_detail->ins_job_candidateid);
          if(empty($job_candidate_confirm->mobility_sent)) {
            $this->recruiterEmail($job_cand_detail, $job_id->prepared_by_email);
            $mobility_sent = date('Y-m-d H:i:s');
            $job_candidate_confirm->mobility_sent = $mobility_sent;
            $job_candidate_confirm->save();
          }
        }
      }
    }
  }

  public function recruiterEmail($details, $prepared_by_email) {
    $foldername = str_slug($details[0]->job_title);
    if (!File::isDirectory(storage_path()."/".$foldername)) {
      mkdir(storage_path(). "/".$foldername ,0777,true);
    }
    $repository = new ResumeRepository();
    $recemail = '';
    foreach($details as $detail) {
      $recemail = $detail->prepared_by_email;
      $name = $detail->first_name.' '.$detail->last_name;
      $name1 = str_slug($name, '-'); //$foldername.$name;
      $final_folder_name = storage_path(). "/".$foldername ."/". $name1 ;
      $path_to_folder = $final_folder_name;
      if(!File::isDirectory($final_folder_name)) {
        mkdir($final_folder_name ,0777,true);
        $path_to_folder =$final_folder_name;
      }
      $get_resume = $this->repo->getResume($detail->candidate_id,$detail->job_id);
      if(!empty($get_resume)) {
        $repository->storeResume($get_resume->resume_url,$get_resume->resume_name,$detail->candidate_id,$path_to_folder);
      }
      $get_covering = $this->repo->getCoveringLetter($detail->candidate_id,$detail->job_id);
      if(!empty($get_covering)) {
        $repository->storeResume($get_covering->coveringletter_url,$get_covering->covering_letter_name,$detail->candidate_id,$path_to_folder);
      }
      $get_suppor = $this->repo->getSupportingDoc($detail->candidate_id,$detail->job_id);
      if(!empty($get_suppor)) {
        $repository->storeResume($get_suppor->url,$get_suppor->name,$detail->candidate_id,$path_to_folder);
      }
    }
    $zipper = new \Chumper\Zipper\Zipper;
    $files= storage_path()."/".$foldername;
    $zipname = storage_path()."/".$foldername.'.zip';
    $zip = $zipper->make($zipname)->folder($foldername)->add($files);
    $zippath = storage_path()."/".$foldername.'.zip';
    $emailService = new EmailRepository();
    $message = View::make('site/email/recruiter-schedule',array('details' => $details, 'noCoaching' => true))->render();
    $subject = 'Mobility assessment applications for '.$details[0]->job_title . ' (req. no ' . $details[0]->vacancy_reference_id . ')';
    $to = $prepared_by_email;
    $recruiter_email_override = DB::table('runtime_config')->where('name', '=', 'recruiter_email_override')->first();
    if(!empty($recruiter_email_override->value)){
      $recruiter_email_override->value = filter_var($recruiter_email_override->value, FILTER_SANITIZE_EMAIL);
      if(filter_var($recruiter_email_override->value, FILTER_VALIDATE_EMAIL)){
        $to = $recruiter_email_override->value;
      }
    }
    $from = Config::get('ins_emails.recruit_email.from');
    $attachments = array($zippath,);
    echo $to ." ==" .$details[0]->job_title;
    $emailService->sendAttachment($to, $from ,$subject , '' ,$message ,$attachments);
  }
}
