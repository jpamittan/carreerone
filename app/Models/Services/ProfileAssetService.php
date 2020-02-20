<?php
namespace App\Models\Services;

use App\Libraries\Sanitize;
use App\Models\Containers\DataObject;
use Format;
use App\Models\Repositories\RepositoryBase;
use App\Models\Containers\ResumeExtract;
use App\Models\Gateways\RedactGateway;
use App\Models\Proxies\FileProxy;
use App\Models\Gateways\Redact\ResumeRedact;
use DB;
use App\Models\Entities\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Config, Redirect, Session, URL, Validator, View;
use App\Models\Factories\ExternalFileFactory;
use App\Models\Repositories\UserPushRepository;

class ProfileAssetService {
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
   $this->repo = app()->make('App\Models\Repositories\ProfileRepository');
  }
    
  public function getLocation() {
    return $this->repo->getLocation();
  }

  public function getCategoryCount() {
    return $this->repo->getCategoryCount();
  }
      
  public function getCategory($keywords) {
    return $this->repo->getCategory($keywords);     
  }

  public function profileEdit($input) {
   $typeid = $this->repo->getUserTypeID();
   $type_id = $typeid->type;
    if ($type_id == 'EiT') {
     $this->repo->profileEmplyeeEdit($input);
     $this->repo->userEdit($input);
     $userID = Auth::id();
     $user_detail =  DB::table('users')->where('id','=',$userID)->first();
      if (!empty($user_detail)) {
        //Push to crm
       $crnpush = new UserPushRepository();
       $fields = [
          ['name'=>'ins_preferredcontactnumber', 'value' => $input['phonumber'], 'type'=>'string'],
          ['name'=>'new_personalmobilenumber', 'value' => $input['mobilenumber'], 'type'=>'string'],
          ['name'=>'new_personalemail', 'value' => $input['personalemail'], 'type'=>'string'],
          ['name' => 'new_emergencycontactname', 'value' => $input['new_emergencycontactname'], 'type' => 'string'],
          ['name' => 'new_emergencycontactnumber', 'value' => $input['new_emergencycontactnumber'], 'type' => 'string'],
          ['name' => 'new_emergencyemail', 'value' => $input['new_emergencyemail'], 'type' => 'string'],
          ['name' => 'new_emergencyrelationship', 'value' => $input['new_emergencyrelationship'], 'type' => 'string'],
          ['name' => 'new_atsi', 'value' => (int)$input['new_atsi'], 'type' => 'boolean'],
          ['name' => 'ins_culturallyandlinguisticallydiverse', 'value' => (int)$input['ins_culturallyandlinguisticallydiverse'], 'type' => 'boolean'],   
          ['name' => 'ins_disability', 'value' => (int)$input['ins_disability'], 'type' => 'boolean']      
        ];
        if ($input['ins_reasonableadjustmentrequired'] != '') {
         $fields[] = ['name' => 'ins_reasonableadjustmentrequired', 'value' => $input['ins_reasonableadjustmentrequired'], 'type' => 'string'];
        } else {
         $fields[] = ['name' => 'ins_reasonableadjustmentrequired', 'value' => 'No', 'type' => 'string'];
        }
       $employee_id  = $user_detail->crm_user_id;
       $crnpush->pushEmployeeIndividual($employee_id , $fields);
      }
    } else if ($type_id == 'Individual') {
      return $this->repo->profileClientsEdit($input);
      return $this->repo->userEdit($input);
    }       
  }
  
  public function deleteCategory($id) {
    return $this->repo->deleteCategory($id);       
  }
  
  public function deleteUserLocation($id) {
    return $this->repo->deleteUserLocation($id);      
  }
  
  public function deleteUserResume($id) {
   $userID = Auth::id();
   $resume = DB::table('ins_cv')
      ->where('id', '=', $id)
      ->where('candidate_id','=',$userID)
      ->first();
    if (! $resume) {
      return false;
    }
    if ($resume->uploaded_to_monster) {
     $user = DB::table('users')->where('id', '=', $userID)->first();
     $soap = View::make('site/partials/monster-resume-delete', [
          'user' => $user,
          'category' => $resume->category_id
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
    }
    return $this->repo->deleteUserResume($id);        
  }

  public function getJobDet($job_id) {
    return $this->repo->getJobDet($job_id);
  }

  public function getSuburbs() {
    return $this->repo->getSuburbs();
  }

  public function postCategory($category) {
    return $this->repo->postCategory($category);
  }

  public function postCategoryPending($category, $msg) {
    return $this->repo->postCategoryPending($category, $msg);
  }

  public function postSkillAssesment($skills) {
   $userID = Auth::id();
    DB::table('ins_user_skill_assesment')->where('candidate_id','=',$userID)->delete();
    foreach($skills as $skill) {
     $this->repo->postSkillAssesment($skill);
    }
  }

  public function postLocation($location) {
    return $this->repo->postLocation($location);      
  }

  public function postJobCapabilitySkills($capabilities,$job_id) {
    return $this->repo->postJobCapabilitySkills($capabilities,$job_id);     
  }

  public function postUserCapability($capabilities,$userID) {
    return $this->repo->postUserCapability($capabilities,$userID);
  }

  public function findusercrmid($userID) {
    return $this->repo->findusercrmid($userID);     
  }
  
  public function postRoleDescription($description,$job_id) {
    return $this->repo->postRoleDescription($description,$job_id);
  }
  
  public function postJobAgencyDetails($agencyDetails,$job_id) {
    $get_agency_id = $this->repo->getAgencyJobID($job_id);
    if (!empty($get_agency_id)) {
      $id = $get_agency_id->agency_id;
      return $this->repo->postJobAgencyDetails($agencyDetails,$id);
    }
  }
       
  public function addSkillAssessment($data) {
    return $this->repo->addSkillAssessment(Auth::id(), $data);
  }

  public function getSkillAssessment($id) {
    return $this->repo->getSkillAssessment($id, Auth::id());
  }
}
