<?php

namespace App\Models\Repositories;

use App\Models\Entities\Jobs;
use App\Models\Entities\AgencyDetails;
use App\Models\Entities\JobMatch;
use App\Models\Entities\JobCandidateID;
use App\Models\Entities\RuntimeConfig;
use Illuminate\Support\Facades\Auth;
use App\Models\Repositories\EmailRepository;
use App\Models\Services\PushJobmatchStatus;
use Carbon\Carbon;
use DB, Mail, View, Config;

class jobMatchImportRepository extends RepositoryBase {
	public function importJobMatch($jobMatch) {
    try {
      $counter = 0;
      foreach($jobMatch as $ins_match) {
        if(isset($ins_match['statecode']['bValue']) && $ins_match['statecode']['bValue'] == 0){
          if(isset($ins_match['new_jobid']['bId'])){
            $id = $ins_match['new_jobid']['bId'];
            $job_id_obj = $this->getJobId($id);
            if(!empty($job_id_obj)){
              $job_id = $job_id_obj->id;
              $job_det = $this->getJobdetails($job_id);
              if(isset($ins_match['new_eitid']['bId'])){
                $user_bid = $ins_match['new_eitid']['bId'];
                $user_id = $this->getUserID($user_bid);
                if(!empty($user_id) && isset($ins_match['ins_matchstatus'])) {
                  $counter++;
                  echo "Matched [user_id: ".$user_id->user_id." ][job_id: ".$job_id." ][match_status: ".$ins_match['ins_matchstatus']['bValue']." ]\n";
                  $job_match = JobMatch::firstOrNew(['new_jobmatchedid' => $ins_match['new_jobmatchedid']]);
                  $job_match->job_id = $job_id;
                  $job_match->candidate_id = $user_id->user_id;
                  $job_match->match_status = $ins_match['ins_matchstatus']['bValue'];
                  $job_match->new_jobmatchedid = $ins_match['new_jobmatchedid'];
                  $job_match->save();
                  if($ins_match['ins_matchstatus']['bValue'] == 121660001) {
                    $count = 0;
                    $count = $count + 1;
                    $mytime = date("Y-m-d");
                    $this->sendReruiterEmail($id, $job_det, $count);
                    $jobmatchstatus = new PushJobmatchStatus();
                    $jobmatchstatus->pushJobMatchStatus($ins_match['new_jobmatchedid'], $status = 121660002);
                    DB::table('ins_jobmatch')->where('new_jobmatchedid', '=', $ins_match['new_jobmatchedid'])->update(['email_limit' => $count, 'email_date' => $mytime]);
                  } else if($ins_match['ins_matchstatus']['bValue'] == 121660021) {
                    //else if the ins_matchstatus from CRM is 121660021
                    $deadline_date_parse = strtotime($job_det->deadline_date. "15:30:00");
                    $deadline_date = date('Y-m-d H:i:s', $deadline_date_parse);
                    $now = date('Y-m-d H:i:s');
                    //Then compare the ins_job.deadline date to current datetime. If current datetime is on or more deadline date plus 15:30:00 send mobility request email
                    if($now >= $deadline_date){
                      $eits = DB::table('ins_employees')
                        ->join('ins_jobmatch', 'ins_jobmatch.candidate_id', '=', 'ins_employees.id')
                        ->where('ins_jobmatch.job_id', '=', $job_id)
                        ->select('ins_employees.*')
                        ->get();
                      $agency = DB::table('ins_agency_details')
                        ->where('id', '=', $job_det->agency_id)
                        ->first();
                      $message = View::make(
                        'site/email/match-notification', [
                            'noCoaching' => true, 
                            'job' => $job_det, 
                            'eits' => $eits,
                            'agency' => $agency
                        ])->render();
                      $subject = 'Mobility request for ' . $job_det->job_title . ' (req no. ' . $job_det->vacancy_reference_id . ')';
                      $attachments = [];
                      foreach(new \DirectoryIterator(storage_path() . '/match-notification') as $file) {
                        if ($file->isDot()) { continue; }
                        $attachments[] = $file->getPathname();
                      }
                      $to = Config::get('ins_emails.match_notification.to');
                      $from = Config::get('ins_emails.match_notification.from');
                      $emailService = new EmailRepository();
                      $emailService->sendAttachment($to, $from, $subject, '', $message, $attachments);
                    }//if ins_job.deadline_date
                  } else if($ins_match['ins_matchstatus']['bValue'] != 121660004) {
                    $this->displayRejectedJob($user_id->user_id, $job_id);
                  }//else if
                }//if $user_id has query result and $ins_match['ins_matchstatus'] has value
              }//if $ins_match['new_eitid']['bId'] has value
              if(!empty($job_id)) {
                app('App\Models\Services\JobTitleSkillMatch')->skillMatchJobForCandidate($job_id);
              }//if
            }//if CRM job is in our ins_jobs table
          }//if
        }//if
      }//foreach
      echo "Job match total: ".$counter."\n";
    } catch (Exception $e) {
      print_r( $e->getMessage() );
      print_r( $e->getLine() );
      print_r( $e->getFile() );
    }
  }

  public function checkJobRDUpload($jobMatch) {
    try{
      foreach($jobMatch as $ins_match){
        $id = $ins_match['new_jobid']['bId'];
        $job_id_obj = $this->getJobId($id);
        if (!empty($job_id_obj)){
          $job_id = $job_id_obj->id;
          $job_det = $this->getJobdetails($job_id);
          if(isset($ins_match['ins_matchstatus'])){
            if ($ins_match['ins_matchstatus']['bValue'] == 121660001 || $ins_match['ins_matchstatus']['bValue'] == 121660002 ){
              $mytime = date("Y-m-d");
              $chk = $this->chkuser($ins_match['new_jobmatchedid']);
              if (!empty($chk)){
                $count = $chk->email_limit;
                if ($count <= 3 ){
                  if (!empty($job_det)){
                    $deadline = $job_det->deadline_date;
                    if(!empty( $deadline)){
                      if($deadline > $mytime){
                        $count = $count + 1;
                      } elseif($deadline == $mytime) {
                          $count = 3;
                      }
                    }
                    $this->sendReruiterEmail($id, $job_det, $count);
                  }
                  $jobmatchstatus = new PushJobmatchStatus();
                  $jobmatchstatus->pushJobMatchStatus($ins_match['new_jobmatchedid'], $status = 121660002);
                  DB::table('ins_jobmatch')->where('new_jobmatchedid', '=', $ins_match['new_jobmatchedid'])->update(['email_limit' => $count, 'email_date' => $mytime]);
                }
              }
            }
          }
        }
      }
    } catch (Exception $e) {
      print_r( $e->getMessage() );
      print_r( $e->getLine() );
      print_r( $e->getFile() );
    }
  }

  public function displayRejectedJob($user_id,$job_id) {
    DB::table('ins_user_rejected_jobs')->where('candidate_id','=',$user_id)->where('job_id','=',$job_id)->delete();
  }

  public function sendReruiterEmail($id,$job_det,$count) {
    if (!empty($job_det->prepared_by_email)){
      $emailService = new EmailRepository();
      switch($count){
        case 1 : $message = View::make('site/email/request-roledescription', array('noCoaching' => true, 'id' => $id, 
        'job_det' => $job_det))->render();
           $subject = 'RD request for ' . $job_det->job_title . ' (req no. ' . $job_det->vacancy_reference_id . ')';
          break;
        case 2 : $message = View::make('site/email/request-roledescription-2', array('noCoaching' => true, 'id' => $id, 
        'job_det' => $job_det))->render();
           $subject = 'RD request for ' . $job_det->job_title . ' (req no. ' . $job_det->vacancy_reference_id . ')';
          break;
        case 3 : $message = View::make('site/email/request-roledescription-3', array('noCoaching' => true, 'id' => $id, 
        'job_det' => $job_det))->render();
           $subject = 'Urgent RD request for ' . $job_det->job_title . ' (req no. ' . $job_det->vacancy_reference_id . ')';
          break;
        default: $message = View::make('site/email/request-roledescription', array('noCoaching' => true, 'id' => $id, 
        'job_det' => $job_det))->render();
           $subject = 'RD request for ' . $job_det->job_title . ' (req no. ' . $job_det->vacancy_reference_id . ')';
          break;
      }
      $to = $job_det->prepared_by_email;
      $recruiter_email_override = DB::table('runtime_config')->where('name', '=', 'recruiter_email_override')->first();
      if(!empty($recruiter_email_override->value)){
        $recruiter_email_override->value = filter_var($recruiter_email_override->value, FILTER_SANITIZE_EMAIL);
        if(filter_var($recruiter_email_override->value, FILTER_VALIDATE_EMAIL)){
          $to = $recruiter_email_override->value;
        }
      }
      $from = Config::get('ins_emails.recruiter_email.from');
      $emailService->send($message, $subject, $from, $to);
    }
  }

  public function chkuser($new_jobmatchedid) {
    return DB::table('ins_jobmatch')->where('new_jobmatchedid','=',$new_jobmatchedid)->select(['ins_jobmatch.*'])->first();
  }

  public function getJobId($id) {
    return DB::table('ins_jobs')->where('jobid','=',$id)->select(['id'])->first();
  }

  public function getUserID($user_bid) {
    return DB::table('ins_employees')->where('employeeid','=',$user_bid)->select(['ins_employees.*'])->first();
  }

  public function getJobdetails($job_id) {
    $jobs =   DB::table('ins_jobs')->leftJoin('ins_agency_details', function($join)
    {
      $userID = Auth::id();
      $join->on('ins_agency_details.id', '=', 'ins_jobs.agency_id');
    })
      ->leftJoin('ins_job_category', function($join)
    {
      $userID = Auth::id();
      $join->on('ins_job_category.id', '=', 'ins_jobs.job_category_id');
    })
      ->where('ins_jobs.id' ,'=', $job_id)
      ->where('ins_agency_details.is_active' ,'1')
      ->select('ins_jobs.*','ins_agency_details.agency_name','ins_job_category.category_name')->distinct()->first();
    return $jobs;
  }

  public function importJobMatchEOI($jobMatch) {
    foreach($jobMatch as $ins_match) {
      $id = $ins_match['new_jobmatchedid'];
      $job_id_obj = DB::table('ins_job_candidate_eoi')->where('ins_job_apply_id', '=', $id)->first();
      if (!empty($job_id_obj)) {
        $eoi_id = $job_id_obj->id;
        if (isset($ins_match['ins_matchstatus'])) {
          DB::table('ins_job_candidate_eoi')->where('id', '=', $eoi_id)->update([
            'submit_status' => $ins_match['ins_matchstatus']['bValue']
          ]);
        }
      }
    }
  }

  public function importJobsAppliedDraft($jobMatchDraft) {
    foreach($jobMatchDraft as $ins_match) {
      $id = $ins_match['new_jobappliedid'];
      $job_id_obj = DB::table('ins_job_candidate')->where('ins_job_apply_id', '=', $id)->where('submit_status', '=', 0)->first();
      if (!empty($job_id_obj)) {
        $eoi_id = $job_id_obj->id;
        if (isset($ins_match['ins_progress']) && $ins_match['ins_progress']['bValue'] == '121660002'){
          DB::table('ins_job_candidate')->where('id', '=', $eoi_id)->update([
            'submit_status' => 2
          ]);
        }
      }
    }
  }

  public function importJobsINSProgress($jobMatchDraft) {
    foreach($jobMatchDraft as $ins_match) {
      $id = $ins_match['new_jobappliedid'];
      $is_active = isset($ins_match['statuscode']['bValue']) ? $ins_match['statuscode']['bValue'] : 0;
      $ins_progress = isset($ins_match['ins_progress']['bValue']) ? $ins_match['ins_progress']['bValue'] : 121660000;
      $jobid = isset($ins_match['new_jobid']['bId']) ? $ins_match['new_jobid']['bId'] : 0;
      $candid = isset($ins_match['new_eitid']['bId']) ? $ins_match['new_eitid']['bId'] : 0;
      $jid = 0;
      $job_id =  DB::table('ins_jobs')->where('jobid', '=', $jobid)->first();
      if(!empty($job_id)){
        $jid = $job_id->id;
      }
      $candidate_id = DB::table('users')->where('crm_user_id', '=', $candid)->first();
      $cid = 0;
      if(!empty($candidate_id)){
        $cid = $candidate_id->id;
      }
      if($is_active  == 1){ //IF Active 
        $job_id_obj = DB::table('ins_job_candidate')->where('ins_job_apply_id', '=', $id)-> first();
        if (!empty($job_id_obj)){
          DB::table('ins_job_candidate')->where('id', '=', $job_id_obj->id)->update([
            'ins_progress' => $ins_progress,
            'updated_at' => date('Y-m-d H:i:s'),
          ]);
        } else {
          $job_id_obj = DB::table('ins_job_candidate')->where('candidate_id', '=', $cid)->where('job_id', '=', $jid)-> first();
          if (!empty($job_id_obj)) {
            $ins_job_apply_id =$id;
            DB::table('ins_job_candidate')->where('id', '=', $job_id_obj->id)->update([
              'ins_progress' => $ins_progress,
              'updated_at' => date('Y-m-d H:i:s'),
              'ins_job_apply_id' => $ins_job_apply_id,
            ]);
          } else {
            $ins_job_apply_id =$id;
            if($cid  != 0 ){
              DB::table('ins_job_candidate')->insert([
                'ins_progress' => $ins_progress,
                'job_id' => $jid,
                'candidate_id' => $cid,
                'ins_job_apply_id' => $ins_job_apply_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
              ]);
            }
          }
        }
      }         
    }
  }
}
