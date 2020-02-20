<?php
namespace App\Models\Services;

use App\Libraries\Sanitize;
use App\Models\Containers\DataObject;
use App\Models\Entities\User;
use App\Models\Entities\CandidateSkillMatch;
use App\Models\Entities\InterviewConfirmation;
use App\Models\Gateways\Email\AWSEmail;
use App\Models\Repositories\RepositoryBase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use DB, Config, Format, Redirect, Session, URL, Validator, View, Mail;

class EmailCandidateService {
  /**
   * Response object with details
   */
  private $response = null;
  protected  $email_service;
  /**
   * Response object with details
   */
  private $repo = null;

  /**
   * ALl class dependencies in the constructor
   */
  public function __construct() {
      $this->repo = app()->make('App\Models\Repositories\EmailCandidateRepository');
  }

  public function emailCandidate() {
    $emailCandidate = $this->repo->sendEmailCandidate();
    if($emailCandidate) {
      //do email functionality here
      foreach ($emailCandidate as $row) {
        $interviews_id    = $row->id;
        $int_confirm = InterviewConfirmation::find($interviews_id);
        if(empty($int_confirm->feedback_sent)) {
          $id               = $row->candidate_id;
          $job_id           = $row->job_id;
          $getUserInfo      = $this->repo->getUserCandidate($id);
          $getJobDetails    = $this->repo->getJobsDetails($job_id);
          $agency_id        = $getJobDetails->agency_id;
          $agencyDetails    = $this->repo->getAgencyDetails($agency_id);
          $getTimeInterview = explode('-',$row->interview_time);
          $timeFormatted    = '';
          foreach($getTimeInterview as $value) {
             $time          = $row->interview_date.' '.$value.':00';
             $timeFormatted .= date('h:i A', strtotime($time)).' - ';
          }
          if($timeFormatted != '') {
            $timeFormatted = rtrim($timeFormatted,' - ');
          }
          $arr_data = array(
            'interviews_id' => $interviews_id,
            'crm_user_id' => $getUserInfo->crm_user_id,
            'email' => $getUserInfo->email,
            'firstname' => $getUserInfo->first_name,
            'lastname' => $getUserInfo->last_name,
            'interview_date' => date('d F', strtotime($row->interview_date)),
            'interview_time' => $timeFormatted,
            'role_description' => htmlspecialchars(filter_var($getJobDetails->role_description)),
            'job_title' => $getJobDetails->job_title,
            'location' => $getJobDetails->location,
            'agency_name' => $agencyDetails->agency_name
          );
          $from       = Config::get('ins_emails.email_candidate.from');
          $to         = $arr_data['email'];
          $message    = View::make('site/email/email-candidate',array(
              'details' => $arr_data,
              'case_manager' => !empty($getUserInfo->employee) && !empty($getUserInfo->employee->caseManager) ? $getUserInfo->employee->caseManager : null
          ))->render();
          $subject    = 'How did your interview go for '.$arr_data['job_title'].' with '.$arr_data['agency_name'].' today';
          $email_gateway = new AWSEmail();
          $email_gateway->send($to, $from, $subject, $message, array());
          //update feedback email sent
          $feedback_sent = date('Y-m-d H:i:s');
          $int_confirm->feedback_sent = $feedback_sent;
          $int_confirm->save();
        }
      }
    }
  }

  public function interviewReminderEmailCandidate() {
    $emailCandidate = $this->repo->interviewReminderEmailCandidate();
    if($emailCandidate) {
      //do email functionality here
      foreach ($emailCandidate as $row) {
        $interviews_id    = $row->id;
        $int_confirm = InterviewConfirmation::find($interviews_id);
        if(empty($int_confirm->reminder_sent)) {
          $id               = $row->candidate_id;
          $job_id           = $row->job_id;
          $getUserInfo      = $this->repo->getUserCandidate($id);
          $getJobDetails    = $this->repo->getJobsDetails($job_id);
          $agency_id        = $getJobDetails->agency_id;
          $agencyDetails    = $this->repo->getAgencyDetails($agency_id);
          $getTimeInterview = explode('-',$row->interview_time);
          $timeFormatted    = '';
          foreach($getTimeInterview as $value) {
             $time          = $row->interview_date.' '.$value.':00';
             $timeFormatted .= date('h:i A', strtotime($time)).' - ';
          }
          if($timeFormatted != '') {
            $timeFormatted = rtrim($timeFormatted,' - ');
          }
          $caseManager = !empty($getUserInfo) && !empty($getUserInfo) && !empty($getUserInfo->employee)
          && !empty($getUserInfo->employee->caseManager) ? $getUserInfo->employee->caseManager : null;
          $arr_data = array(
            'interviews_id' => $interviews_id,
            'crm_user_id' => $getUserInfo->crm_user_id,
            'email' => $getUserInfo->email,
            'firstname' => $getUserInfo->first_name,
            'lastname' => $getUserInfo->last_name,
            'interview_date' => date('d F', strtotime($row->interview_date)),
            'interview_time' => $timeFormatted,
            'role_description' => htmlspecialchars(filter_var($getJobDetails->role_description)),
            'job_title' => $getJobDetails->job_title,
            'location' => $getJobDetails->location,
            'agency_name' => $agencyDetails->agency_name,
            'case_manager' => $caseManager
          );
          $from       = Config::get('ins_emails.email_candidate.from');
          $to         = $arr_data['email'];
          $message    = View::make('site/email/interview-reminder-email',array(
              'details'=>$arr_data,
              'case_manager' => !empty($getUserInfo->employee) && !empty($getUserInfo->employee->caseManager) ? $getUserInfo->employee->caseManager : null
          ))->render();
          $subject    = 'Interview reminder for '.$arr_data['job_title'].' with '.$arr_data['agency_name'].' ';
          $email_gateway  = new AWSEmail();
          $email_gateway->send($to, $from, $subject, $message, array());
          $reminder_sent = date('Y-m-d H:i:s');
          $int_confirm->email_reminder = $int_confirm->email_reminder + 1;
          $int_confirm->reminder_sent = $reminder_sent;
          $int_confirm->save();
        }
      }
    }
  }

  public function sendFeedback($inputs) {
    $rules = array('feedback' => "Required");
    $validator  = Validator::make($inputs,$rules);
    if(!$validator->passes()) {
        return false;
    }
    $sendFeedback = $this->repo->sendFeedback($inputs);
    if($sendFeedback['returns']) {
      $sendFeedback['feedback']     = addslashes(htmlentities($inputs['feedback'],ENT_QUOTES));
      $from       = Config::get('ins_emails.email_feedback.from');
      $to         = $sendFeedback['prepared_by_email'];
      $message    = View::make('site/email/candidate-feedback',array('details'=>$sendFeedback))->render();
      $subject    = 'Feedback by the '.$sendFeedback['fullname'].' for the role '.$sendFeedback['agency_name'];
      $email_gateway  = new AWSEmail();
      $email_gateway->send($to, $from, $subject, $message, array());
      //pushed to crn
      $ins_interviews_calandar_id = base64_decode($inputs['id']);
      $crm_user_id =  ($inputs['crm_id']);

      $user = \DB::table('users')->where("crm_user_id",'=',$crm_user_id)->first();
      $userid = $user->id ;
      $ins_interviews_calandar = \DB::table('ins_interviews_calandar')->where("id",'=',$ins_interviews_calandar_id)->first();
      $jobid = $ins_interviews_calandar->job_id;
      $ins_job_candidate = \DB::table('ins_job_candidate')->where("candidate_id",'=',$userid)->where("job_id",'=',$jobid)->first();
      $ins_job_apply_id = $ins_job_candidate->ins_job_apply_id;
      $data['ins_job_apply_id'] =  $ins_job_apply_id;
      $data['interviewfeedbackdetails'] = $sendFeedback['feedback'];
      $service = app()->make('App\Models\Services\CrmSyncDataService');
      $service->updateInterviewFeedback($data);
      return true;
    }
    return false;
  }
}
