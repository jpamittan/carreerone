<?php

namespace App\Models\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use App\Models\Entities\Employee;
use App\Models\Entities\SkillAssesment;
use App\Models\Entities\CapabilityJob;
use App\Models\Entities\CandidateCapability;
use App\Models\Entities\User;
use App\Models\Factories\ExternalFileFactory;
use App\Models\Repositories\RepositoryBase;
use Carbon\Carbon;
use DB, Config, Redirect, Session, URL, Validator, View;

class ProfileRepository extends RepositoryBase {
  public function getRssFeeds() {
    return DB::table('ins_rss')->select(['ins_rss.*'])->get();
  }

  public function getLocation() {
    $userID = Auth::id();
    return DB::table('ins_user_job_locations')->where('user_id', '=', $userID)->select(DB::raw('COUNT(id) as count'))->first();
  }

  public function getCategoryCount() {
    $userID = Auth::id();
    return DB::table('ins_user_job_category_types')->where('user_id', '=', $userID)->select(DB::raw('COUNT(id) as count'))->first();
  }

  public function getCategory($keywords) {
    return DB::table('ins_job_category_types')->where('is_active' ,'1')->where('category_type_name', 'LIKE', "%$keywords%")->select(['category_type_name'])->get();
  }

  public function getUserTypeID() {
    $userID = Auth::id();
    return DB::table('users')->where('id', '=', $userID)->select(['type'])->first();
  }

  public function profileEmplyeeEdit($input) {
    $userID = Auth::id();
    return DB::table('ins_employees')
      ->where('user_id','=', $userID)
      ->update(['new_personalhomenumber' =>$input['phonumber'],
      'new_personalmobilenumber' => $input['mobilenumber'],
      'new_personalemail' => $input['personalemail'],
      'new_emergencycontactname' => $input['new_emergencycontactname'],
      'new_emergencycontactnumber' => $input['new_emergencycontactnumber'],
      'new_emergencyemail' => $input['new_emergencyemail'],
      'new_emergencyrelationship' => $input['new_emergencyrelationship'],
      'new_atsi' => $input['new_atsi'],
      'ins_culturallyandlinguisticallydiverse' => $input['ins_culturallyandlinguisticallydiverse'],
      'ins_disability' => $input['ins_disability'],
      'ins_reasonableadjustmentrequired' => $input['ins_reasonableadjustmentrequired']
    ]);
  }

  public function profileClientsEdit($input) {
    $userID = Auth::id();
    return DB::table('ins_clients')
    ->where('user_id','=', $userID)
    ->update(['mobile_number' => $input['mobilenumber'],
      'phone_number' => $input['phonumber']
    ]);

  }

  public function userEdit($input) {
    $userID = Auth::id();
    DB::table('users')
    ->where('id','=', $userID)
    ->update(['email' => $input['personalemail']]);
  }
        
  public function deleteCategory($id) {
    $userID = Auth::id();
    return DB::table('ins_user_job_category_types')
    ->where('user_id','=', $userID)
    ->where('job_category_type_id','=', $id)
    ->delete();        
  }
  
  public function deleteUserLocation($id) {
    $userID = Auth::id();
    return DB::table('ins_user_job_locations')
    ->where('user_id','=', $userID)
    ->where('ins_location_id','=', $id)
    ->delete();      
  }

  public function deleteUserResume($id) {
    return DB::table('ins_cv')
    ->where('id','=', $id)
    ->update(['is_latest' => 0,'status'=> 0]);
  }
      
  public function getCategoryID($category) {
    return DB::table('ins_job_category_types')
      ->where('category_type_name','=', $category)
      ->where('is_active' ,'1')
      ->select('id')->first();      
  }
  
  public function postCategory($cate_id) {
    $userID = Auth::id();
    return DB::table('ins_user_job_category_types')->insert(
      ['user_id' => $userID, 'job_category_type_id' =>$cate_id]
    );
  }

  public function postCategoryPending($cate_id , $msg) {
    $userID = Auth::id();
    return DB::table('ins_user_job_category_types')->insert(
      ['user_id' => $userID, 'job_category_type_id' =>$cate_id,
      'message' =>$msg,
      'pending' =>1
      ]
    );
  }

  public function postSkillAssesment($skill) {
    $userID = Auth::id();
    $skill_ass = new SkillAssesment();
    $skill_ass->candidate_id = $userID;
    $skill_ass->skill_asse_type_id = $skill;
    $skill_ass->save();
  }

  public function postLocation($location) {
    $userID = Auth::id();
    return DB::table('ins_user_job_locations')->insert(['user_id'=> $userID,'ins_location_id' => $location]);
  }

  public function postJobCapabilitySkills($capabilities, $job_id) {
    $chk_job_id = $this->chkJobID($job_id);
    if ($chk_job_id == 1) {
      DB::table('ins_capability_job')->where('job_id', '=', $job_id)->delete();
      $this->capabilitiesmatch($capabilities, $job_id);
    } else {
      $this->capabilitiesmatch($capabilities, $job_id);
    }
  }

  public function chkJobID($job_id) {
    return DB::table('ins_capability_job')->where('job_id', '=', $job_id)->exists();
  }

  public function getCapabilityNameId($name) {
    return DB::table('ins_capability_match_names')->where('match_names', '=', $name)->select(['id','group_id'])->first();
  }

  public function getLevelNameId($level) {
    return DB::table('ins_capability_level')->where('level_name', '=', $level)->select(['id','level_id'])->first();
  }

  public function capabilitiesmatch($capabilities, $job_id) {
    if ($capabilities[0]['name'] != 'Display Resilience and Courage') {
      array_splice($capabilities, 0, 1);
    }
    foreach ($capabilities as $capability) {
      try {
        $capability['name'] = preg_replace('/[^\00-\255]+/u', '', $capability['name']);
        if (!empty($capability['name'])) {
          $capability_name_id = $this->getCapabilityNameId(htmlentities($capability['name']));
          $level_id = $this->getLevelNameId($capability['level']);
          if (!empty($capability_name_id)) {
            $job_capability = new CapabilityJob();
            $job_capability->job_id = $job_id;
            $job_capability->capability_name_id = $capability_name_id->id;
            $job_capability->level_id = !empty($level_id) ? $level_id->level_id : 0;
            $job_capability->group_id = $capability_name_id->group_id;
            $job_capability->core_status = !empty($capability['core']) ? $capability['core'] : 0;
            $job_capability->save();
          }
        }
      } catch (\Exception $exception) {
        logger('Error at ' . __CLASS__ . '@' . __FUNCTION__ . ":" . $exception->getMessage());
      }
    }
  }

  public function postRoleDescription($description, $job_id) {
    $description = nl2br($description);
    DB::table('ins_jobs')->where('id', '=', $job_id)->update(['role_description'=>$description]);
  }

  public function getAgencyJobID($job_id) {
    return DB::table('ins_jobs')->where('id', '=', $job_id)
    ->select(['agency_id'])
    ->first();
  }

  public function postJobAgencyDetails($agencyDetails, $id) {
    $agency = isset($agencyDetails['Agency Website']) ? $agencyDetails['Agency Website']:'';
    $role_number = isset($agencyDetails['Role Number']) ? $agencyDetails['Role Number']:'';
    $division = '';
    if (isset($agencyDetails['Division/Branch/Unit'])) {
      $division = $agencyDetails['Division/Branch/Unit'];
    } else if (isset($agencyDetails['Division'])) {
      $agedivisionncy = $agencyDetails['Division'];
    }
    return DB::table('ins_agency_details')->where('id', '=', $id)
      ->update([
        'anzsco_code' => isset($agencyDetails['ANZSCO Code']) ? $agencyDetails['ANZSCO Code'] :'', 
      'agency_website' => $agency,
      'approval_date' => isset($agencyDetails['Date of Approval']) ? $agencyDetails['Date of Approval'] :'',   
      'pcat_code' => isset($agencyDetails['PCAT Code']) ? $agencyDetails['PCAT Code'] :'',  
      'role_number' => $role_number,
      'kind_employment' => isset($agencyDetails['Kind of Employment']) ? $agencyDetails['Kind of Employment'] :'',
      'Division' => $division
    ]);
  }

  public function postUserCapability($capabilities, $userID) {
    $chk_job_id = $this->chkUserID($userID);
    if ($chk_job_id == 1) {
      DB::table('ins_capability_candidate')->where('candidate_id', '=', $userID)->delete();
    }
    $this->usercapabilitiesmatch($capabilities, $userID);
  }

  public function findusercrmid($userID) {
    return DB::table('users')
    ->where('id','=', $userID)
    ->select('crm_user_id')
    ->first();
  }

  public function chkUserID($userID) {
    return DB::table('ins_capability_candidate')
    ->where('candidate_id', '=', $userID)
    ->exists();
  }

  public function usercapabilitiesmatch($capabilities, $userID) {
    if ($capabilities[0]['name'] != 'Display Resilience and Courage') {
      array_splice($capabilities, 0, 1);
    }
    foreach($capabilities as $capability) {
      $capability['name']= preg_replace('/[^\00-\255]+/u', '', $capability['name']);
      if (!empty($capability['name'])) {
        $capability_name_id= $this->getCapabilityNameId(htmlentities($capability['name']));
        $level_id= $this->getLevelNameId($capability['level']);
        if (!empty($capability_name_id)) {
          $user_capability = new CandidateCapability();
          $user_capability->candidate_id = $userID;
          $user_capability->capability_name_id = $capability_name_id->id;
          $user_capability->level_id = !empty($level_id) ? $level_id->level_id:0;
          $user_capability->core = !empty($capability['core']) ? $capability['core']:0;
          $user_capability->save();
        }
      }
    }
  }

  public function getJobDet($job_id) {
    $jobs = DB::table('ins_jobs')
    ->leftJoin('ins_agency_details', function($join) {
      $userID = Auth::id();
      $join->on('ins_agency_details.id', '=', 'ins_jobs.agency_id');
    })
    ->leftJoin('ins_job_category', function($join) {
      $userID = Auth::id();
      $join->on('ins_job_category.id', '=', 'ins_jobs.job_category_id');
    })
    ->where('ins_jobs.id' ,'=', $job_id)
    ->where('ins_agency_details.is_active' ,'1')
    ->where('ins_job_category.is_active' ,'1')
    ->select('ins_jobs.*','ins_agency_details.agency_name','ins_job_category.category_name')
    ->distinct()
    ->first();
    return $jobs;
  }

  public function getSuburbs() {
    $jobs = DB::table('ins_suburbs')
    ->where('is_active' ,'=', 1)
    ->orderBy('suburb' ,'asc')
    ->get();
    return $jobs;
  }

  public function addSkillAssessment($id, $data) {
    $skillAssessment = new SkillAssesment();
    $skillAssessment->candidate_id = $id;
    $skillAssessment->skill_asse_type_id = $data['skill_id'];
    $skillAssessment->recency_id = $data['recency_id'];
    $skillAssessment->frequency_id = $data['frequency_id'];
    $skillAssessment->level_id = $data['level_id'];
    $skillAssessment->comment = ! empty($data['comment']) ? substr($data['comment'], 0, 2000) : '';
    $skillAssessment->save();
    return $skillAssessment->id;
  }

  public function getSkillAssessment($id, $userId) {
    $skillAssessment = new SkillAssesment();
    return $skillAssessment
    ->where('candidate_id', '=', $userId)
    ->where('active', '=', 1)
    ->where('id', '=', $id)
    ->first();
  }
}
