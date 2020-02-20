<?php

namespace App\Http\Controllers\site;

use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\site\AdminController;
use App\Models\Services\JobAssetService;
use App\Models\Services\CaseManagerAssetService;
use View, Redirect, Response;

class CaseManagerController extends AdminController {
  public function getCandidateDetails($user_id,$job_id) {
    $service = new CaseManagerAssetService();
    $user_id = $service->getuserDetails($user_id);
    $user_message = "Candidate ID is Mismatch";
    $job_message = "JOB ID is Mismatch";
    if (empty($user_id)) {
      return View::make('site.home.error', array('message' => $user_message));
    } else {
      $job_id = $service->getjobDetails($job_id);
      if(empty($job_id)){
        return View::make('site.home.error', array('message' => $job_message));
      } else {
        return View::make(
          'site.home.candidate-info',
          array(
            'user_id' => $user_id->user_id,
            'job_id' => $job_id->id
          )
        );
      }
    }
  }

  public function getCandidateMatchDetails() {
    $input = Input::all();
    if(!isset($input['id'])) {
      $user_message = "ID empty";
      return View::make('site.home.error',array('message' => $user_message));
    }
    $role_match_id = $input['id'];
    $service = new CaseManagerAssetService();
    $role_match = $service->getUserJobIdFromRoleMatch($role_match_id);
    if (empty($role_match)) {
      $user_message = "Data does not exist";
      return View::make('site.home.error', array('message' => $user_message));
    }
    $user_id = $role_match->user_crmid;
    $job_id = $role_match->jobid;
    $user_id = $service->getuserDetails($user_id);
    $user_message = "Candidate ID is Mismatch";
    $job_message = "JOB ID is Mismatch";
    if (empty($user_id)) {
      return View::make('site.home.error', array('message' => $user_message));
    } else {
      $job_id = $service->getjobDetails($job_id);
      if(empty($job_id)){
        return View::make('site.home.error', array('message' => $job_message));
      } else {
        return View::make(
          'site.home.candidate-info',
          array(
            'user_id' => $user_id->user_id,
            'job_id' => $job_id->id
          )
        );
      }   
    } 
  }

  public function getThreeLevelMatching1() {
    $input = Input::all();
    if(!isset($input['id'])){
      $user_message = "ID empty";
      return View::make('site.home.error',array('message'=> $user_message));
    }
    $role_match_id = $input['id'];
    $service = new CaseManagerAssetService();
    $role_match = $service->getLocalUserJobIdFromRoleMatch($role_match_id);
    if (empty($role_match)) {
      $user_message = "Data does not exist";
      return View::make('site.home.error', array('message'=> $user_message));
    }
    $user_id = $role_match->user_crmid;
    $job_id = $role_match->jobid;
    $job_det = $service->getJobDetailsinfo($job_id);
    $job_match = $service->getJobMatch($job_id,$user_id);
    $getcapabilities = $service->getCapabilities($job_id,$user_id);
    $capabilities = [];
    foreach ($getcapabilities as $cap) {
      $capabilities[$cap->group_name][] = [
        'image' => $cap->group_images,
        'capabilities' => $cap->match_names,
        'level' => $cap->level_name,
        'score' => $cap->score,
        'core_status' => $cap->core_status
      ];
    }
    $capability_score = $service->getCapabilityScore($job_id,$user_id);
    $getskills = $service->getskills($job_id,$user_id);
    return View::make(
      'site.partials.three-level-matching',
      array(
        'capabilities'=>$capabilities,
        'score'=>$capability_score,
        'skills'=>$getskills,
      )
    );    
  }

  public function getCandidateInfo($user_id,$job_id) {
    $service = new CaseManagerAssetService();
    $user_det = $service->getuserDetailsinfo($user_id);
    $case_id = $user_det->ownerid;
    $casemanager_det = $service->getCMDetailsinfo($case_id);
    $job_det = $service->getJobDetailsinfo($job_id);
    return View::make(
      'site.partials.interview-info',
      array(
        'user_det' => $user_det,
        'job_det' => $job_det,
        'case_manager' => $casemanager_det
      )
    );
  }

  public function getThreeLevelMatching($user_id,$job_id) {
    $service = new CaseManagerAssetService();
    $job_det = $service->getJobDetailsinfo($job_id);
    $job_match = $service->getJobMatch($job_id, $user_id);
    $getcapabilities = $service->getCapabilities($job_id, $user_id);
    $capabilities = [];
    foreach ($getcapabilities as $cap) {
      $capabilities[$cap->group_name][] = [
        'image' => $cap->group_images,
        'capabilities' => $cap->match_names,
        'level' => $cap->level_name,
        'score' => $cap->score,
        'core_status' => $cap->core_status
      ];
    }
    $capability_score = $service->getCapabilityScore($job_id, $user_id);
    $getskills = $service->getskills($job_id, $user_id);
    return View::make(
      'site.partials.three-level-matching',
      array(
        'job_match' => $job_match,
        'job_det' => $job_det,
        'capabilities' => $capabilities,
        'score' => $capability_score,
        'skills' => $getskills,
      )
    );
  }

  public function getCandidateProfile($user_id,$job_id) {
    $service = new CaseManagerAssetService();
    $geteducation = $service->geteducation($user_id);
    $getworkhistory = $service->getWorkHistory($user_id);
    return View::make(
      'site.partials.candidate-profile-info',
      array(
        'education'=>$geteducation,
        'workhistory'=>$getworkhistory,
      )
    );
  }
}
