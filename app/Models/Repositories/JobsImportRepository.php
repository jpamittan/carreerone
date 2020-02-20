<?php

namespace App\Models\Repositories;

use App\Models\Entities\Jobs;
use App\Models\Entities\AgencyDetails;
use App\Models\Entities\JobCategory;
use App\Models\Entities\JobCategoryType;
use App\Models\Containers\ResumeExtract;
use App\Models\Gateways\RedactGateway;
use App\Models\Proxies\FileProxy;
use App\Models\Gateways\Redact\ResumeRedact;
use App\Models\Gateways\Redact\JobTitleSkillMatchJojari;
use App\Models\Entities\JobSkillMatch;
use App\Models\Entities\SkillMatchNames;
use App\Models\Entities\JobTitleNames;
use DB, DateTimeZone, DateTime;

class JobsImportRepository extends RepositoryBase {
  public function importJobs($jobs) {
    foreach ($jobs as $ins_job) {
      try {
        if(
          (isset($ins_job['new_jobid']) && !empty($ins_job['new_jobid'])) &&
          (isset($ins_job['new_jobtitle']) && !empty($ins_job['new_jobtitle'])) 
        ) {
          $job = Jobs::firstOrNew(['jobid' => $ins_job['new_jobid']]);   
          $job->jobid = array_get($ins_job, 'new_jobid');
          $job->job_title = array_get($ins_job, 'new_jobtitle');
          $job->agency_branch_id = isset($ins_job['new_agencyid']) ? $ins_job['new_agencyid']['bId'] : null;
          if(isset($ins_job['new_agencyid'])) { 
            $agencyDetails = AgencyDetails::where('ins_agency_id', $ins_job['new_agencyid']['bId'])->first();
            if($agencyDetails) {
              $job->agency_id = $agencyDetails->id;
            }
          }
          if(isset($ins_job['new_jobcategoryid'])) {       
            $jobCategory = JobCategory::where('ins_job_category_id', $ins_job['new_jobcategoryid']['bId'])->first();
            if($jobCategory) {
              $job->job_category_id = $jobCategory->id;
            }
          }
          if(isset($ins_job['new_jobcategorytypeid'])) {      
            $jobCategoryType = JobCategoryType::where('ins_job_category_type_id', $ins_job['new_jobcategorytypeid']['bId'])->first();
            if($jobCategoryType) {
              $job->job_category_type_id = $jobCategoryType->id;
            }
          }
          $expiry = null;
          $appreoved_date = array_get($ins_job, 'new_approveddate');
          if(!empty($appreoved_date)) {
            $dt = new DateTime($appreoved_date, new DateTimeZone('UTC'));
            $dt->setTimezone(new DateTimeZone('Australia/Sydney'));
            $appreoved_date = $dt->format('Y-m-d');
          }
          $deadline = array_get($ins_job, 'new_deadlinedate');
          if(!empty($deadline)) {
            $dt = new DateTime($deadline, new DateTimeZone('UTC'));
            $dt->setTimezone(new DateTimeZone('Australia/Sydney'));
            $deadline = $dt->format('Y-m-d');
          }
          if(isset($deadline)) {
            $date = date('Y-m-d H:i:s');
            if($deadline < $date) {
              $expiry = date('Y-m-d H:i:s');
            } else {
              $expiry = null;
            }
          }
          $createdon = array_get($ins_job, 'createdon');
          if(!empty($createdon)) {
            $dt = new DateTime($createdon, new DateTimeZone('UTC'));
            $dt->setTimezone(new DateTimeZone('Australia/Sydney'));
            $createdon = $dt->format('Y-m-d');
          }
          $modifiedon = array_get($ins_job, 'modifiedon');
          if(!empty($modifiedon)) {
            $dt = new DateTime($modifiedon, new DateTimeZone('UTC'));
            $dt->setTimezone(new DateTimeZone('Australia/Sydney'));
            $modifiedon = $dt->format('Y-m-d');
          }
          $job->vacancy_reference_id = array_get($ins_job, 'new_vacancyreference');
          $job->appreoved_date = $appreoved_date;
          $job->job_function = array_get($ins_job, 'new_jobfunction');
          $job->suburb = isset($ins_job['new_suburbid']) ? $ins_job['new_suburbid']['bName'] : null;
          $job->location = isset($ins_job['new_location']) ? $ins_job['new_location']['bName'] : null;
          $job->state = isset($ins_job['statecode']) ? $ins_job['statecode']['bValue'] : null;
          $job->salary_package = array_get($ins_job, 'new_salarypackage');
          $job->salary_from = isset($ins_job['new_salaryfrom_base']) ? $ins_job['new_salaryfrom_base']['bValue'] : null;
          $job->salary_to = isset($ins_job['new_salaryto_base']) ? $ins_job['new_salaryto_base']['bValue'] : null;
          $job->job_grade = isset($ins_job['new_jobgrade']) ? $ins_job['new_jobgrade']['bName'] : null;
          $job->employment_status_id = isset($ins_job['new_employmentstatus']) ? $ins_job['new_employmentstatus']['bValue'] : null;
          $job->position_description = array_get($ins_job, 'new_positiondescription', 'Not Available.');
          if(isset($ins_job['new_selectioncriteria'] ) &&  is_array($ins_job['new_selectioncriteria']  )  ) {
            $job->selection_criteria = '';
          } else {
            $job->selection_criteria = array_get($ins_job, 'new_selectioncriteria');
          }
          $job->enquirey_name = array_get($ins_job, 'new_enquiriesname');
          $job->enquire_number = array_get($ins_job, 'new_enquiriesnumber');
          $job->prepared_by_name = array_get($ins_job, 'new_preparedby');
          $job->prepared_by_number = array_get($ins_job, 'new_preparedbynumber');
          $job->prepared_by_email = array_get($ins_job, 'new_preparedbyemail');
          $job->deadline_date = $deadline; 
          $job->cluster_only = array_get($ins_job, 'new_clusteronly') == "false" ? 'N' : 'Y' ;
          $job->agency_only = array_get($ins_job, 'new_agencyonly') == "false" ? 'N' : 'Y' ;
          $job->is_expired = $expiry;
          if(isset($ins_job['statuscode']['bValue']) &&  $ins_job['statuscode']['bValue'] == 2 ) {
            $job->is_expired = '1970-01-01 00:00:00';
          }
          $job->job_type = isset($ins_job['new_jobtype']) ? $ins_job['new_jobtype']['bValue'] : null;
          $job->created_at = $createdon; 
          $job->updated_at = $modifiedon; 
          $job->jobstatus = isset($ins_job['new_jobstatus']) ? $ins_job['new_jobstatus']['bValue'] : null; 
          $job->save();
          $job_id = $job->id;
          $data['type'] = 'similar';  
          $data['query'] = $ins_job['new_jobtitle'];  
          $type= $data['type'];
          $skillmatch =new JobTitleSkillMatchJojari($type);
          $skills = $skillmatch->clean($data);
          if(!empty($skills)) {
            if(isset($skills['skills']) {
              $this->postJobSkills($skills['skills'],$job_id);
            }
            if(isset($skills['job_title'])) {
              $this->postJobTitle($skills['job_title'],$job_id);
            }
          } 
        }
      } catch (\Exception $e) {
        print_r( $e->getMessage() );
        print_r( $e->getLine() );
        print_r( $e->getFile() );
        continue;      
      }
    }             
  }

  public function postJobTitle($skills,$job_id) {
    DB::table('ins_skillmatch_job')->where('job_id', '=' , $job_id)->where('status', '=' , 2)->delete();
    foreach($skills as $skill) {
      $distance = $skill['distance'];
       $skill_match = DB::table('ins_job_title_name')
                      ->where('job_title', '=', $skill['word'])->select(['ins_job_title_name.*'])
                      ->first();
      if(!empty($skill_match)) {
        $skill_match_name = $skill_match->job_title;
        $skill_count = $skill_match->count;
        $skill_id = $skill_match->id;
        $count = $skill_count+1;
        DB::table('ins_job_title_name')->where('id', '=', $skill_id)->update(['count' => $count]);
      } else {
        $job_title_name = new JobTitleNames();
        $job_title_name->job_title =  $skill['word'];
        $job_title_name->count =  0;
        $job_title_name->save();
        $skill_id = $job_title_name->id;
      }
      $job_skill = new JobSkillMatch();
      $job_skill->skill_id = $skill_id;
      $job_skill->job_id = $job_id;
      $job_skill->distance = $distance;
      $job_skill->status =2;
      $job_skill->save();                           
    }
  }

  public function postJobSkills($skills,$job_id) {
    DB::table('ins_skillmatch_job')->where('job_id', '=' , $job_id)->where('status', '=' , 1)->delete();
    foreach($skills as $skill) {
      $distance = $skill['distance'];
      $skill_match = DB::table('ins_skillmatch_names')
                    ->where('skill_name', '=', $skill['word'])->select(['ins_skillmatch_names.*'])
                    ->first();
      if(!empty($skill_match)) {
        $skill_match_name = $skill_match->skill_name;
        $skill_count = $skill_match->count;
        $skill_id = $skill_match->id;
        $count = $skill_count+1;
        DB::table('ins_skillmatch_names')->where('id', '=', $skill_id)->update(['count' => $count]);
      } else {
        $skill_match_name = new SkillMatchNames();
        $skill_match_name->skill_name =  $skill['word'];
        $skill_match_name->count =  0;
        $skill_match_name->save();
        $skill_id = $skill_match_name->id;
      }
      $job_skill = new JobSkillMatch();
      $job_skill->skill_id = $skill_id;
      $job_skill->job_id = $job_id;
      $job_skill->distance = $distance;
      $job_skill->status =1;
      $job_skill->save();      
    }  
  }
}
