<?php

namespace App\Models\Services;

use App\Libraries\Sanitize;
use App\Models\Containers\DataObject;
use App\Models\Repositories\RepositoryBase;
use App\Models\Entities\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use DB, Config, Format, Redirect, Session, URL, Validator, View;

class CaseManagerAssetService {
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
    $this->repo = app()->make('App\Models\Repositories\CaseManagerMatchRepository');
  }

  public function getUserJobIdFromRoleMatch($role_match_id) {
    $data = NULL;
    $role_match = DB::table('ins_jobmatch')->where('new_jobmatchedid','=',$role_match_id) ->first();
    if (!empty($role_match)) {
      $data = new \stdClass();
      $userid = $role_match->candidate_id;
      $jobid = $role_match->job_id;
      $job = DB::table('ins_jobs')->where('id','=',$jobid) ->first();
      $user = DB::table('users')->where('id','=',$userid) ->first();
      $data->jobid = $job_id = $job->jobid;
      $data->user_crmid  = $user_id = $user->crm_user_id;
    }
    return $data;
  }

  public function getLocalUserJobIdFromRoleMatch($role_match_id) {
    $data = NULL;
    $role_match = DB::table('ins_jobmatch')->where('new_jobmatchedid','=',$role_match_id) ->first();
    if (!empty($role_match)) {
      $data = new \stdClass();
      $userid = $role_match->candidate_id;
      $jobid = $role_match->job_id;
      $job = DB::table('ins_jobs')->where('id','=',$jobid) ->first();
      $user = DB::table('users')->where('id','=',$userid) ->first();
      $data->jobid = $job_id = $job->id;
      $data->user_crmid  = $user_id = $user->id;
    }
    return $data;
  }

  public function getuserDetails($user_id) {
    $user = $this->repo->getuserDetails($user_id);
    if (!empty($user)) {
      $user_id = $user->id;
      $type_id = $user->type;
      if ($type_id == 'EiT') {
        return  $this->repo->getEmployeeDetail($user_id);
      } else if ($type_id == 'Individual') {
        return  $this->repo->getClientsDetail($user_id);
      }
    }
  }

  public function getjobDetails($job_id) {
    return $this->repo->getjobDetails($job_id);
  }

  public function getCMDetailsinfo($id) {
    return $this->repo->getCMDetailsinfo($id);
  }

  public function getuserDetailsinfo($user_id) {
    $user = $this->repo->getuserDetailsinfo($user_id);
    $user_id = $user->id;
    $type_id = $user->type;
    if ($type_id == 'EiT') {
      return  $this->repo->getEmployeeDetail($user_id);
    } else if ($type_id == 'Individual') {
     return  $this->repo->getClientsDetail($user_id);
    }
  }

  public function getJobDetailsinfo($job_id) {
    return $this->repo->getJobDetailsinfo($job_id);
  }

  public function getJobMatch($job_id,$user_id) {
    return $this->repo->getJobMatch($job_id,$user_id);
  }
  
  public function getCapabilities($job_id,$user_id) {
    return $this->repo->getCapabilities($job_id,$user_id);
  }
  
  public function getCapabilityScore($job_id,$user_id) {
    return $this->repo->getCapabilityScore($job_id,$user_id);
  }

  public function getskills($job_id,$userID) {
    DB::table('ins_skillmatch')
    ->where('candidate_id','=',$userID)
    ->where('job_id','=',$job_id)
    ->delete();
    $job_skill =  array();
    $cand_skill =  array();
    $match_status = array();
    $jobskills = $this->repo->getskillByJobID($job_id);
    $candidateskills = $this->repo->getskillByCandidate($userID);
    if (!empty($jobskills) && !empty($candidateskills)) {
      foreach($jobskills as $jobskill) {
        $job_skill[$jobskill->skill_id] = 1;
      }
      foreach($candidateskills as $candidateskill) {
        $cand_skill[$candidateskill->skill_id] = 1;
      }
      foreach($job_skill as $skilljob => $value) {
        if (!isset($cand_skill[$skilljob])) {
        $match_status[$skilljob] = 0;
          $this->repo->insertSkillMatch($match_status,$job_id,$userID);
        } else {
          $match_status[$skilljob] = 1;
          $this->repo->insertSkillMatch($match_status,$job_id,$userID);
        }
      }
    }
    return $this->repo->getSkillMatchCandidate($job_id,$userID);
  }

  public function geteducation($user_id) {
    return $this->repo->geteducation($user_id);
  }

  public function getWorkHistory($user_id) { 
    return $this->repo->getWorkHistory($user_id);
  }

  public function getcandidateSkills($user_id) {
    return $this->repo->getSkills($user_id);
  }
}
