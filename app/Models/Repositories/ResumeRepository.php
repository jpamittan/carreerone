<?php
namespace App\Models\Repositories;

use App\Models\Containers\ResumeExtract;
use App\Models\Entities\User;
use App\Models\Entities\SkillMatchNames;
use App\Models\Entities\CandidateSkillMatch;
use App\Models\Entities\Resumes;
use App\Models\Entities\JobMatchCandidate;
use App\Models\Entities\CategoryNames;
use App\Models\Entities\JobMatchCandidateCategory;
use App\Models\Entities\JobCandidateID;
use App\Models\Entities\CoveringLetter;
use App\Models\Entities\SupportingDoc;
use App\Models\Entities\UserPDFCapability;
use App\Models\Entities\JobRoleDescriprion;
use App\Models\Entities\CandidateEducatioInfo;
use App\Models\Entities\CandidateWorkHistoryInfo;
use App\Models\Entities\CandidatePredictedSkills;
use App\Models\Factories\ExternalFileFactory;
use App\Models\Gateways\RedactGateway;
use App\Models\Gateways\Redact\ResumeRedact;
use App\Models\Proxies\FileProxy;
use App\Models\Services\UpdateJobApplied;
use App\Models\Services\EmailService;
use App\Models\Repositories\EmailRepository;
use App\Models\Repositories\RepositoryBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Config, DB, Redirect, Session, URL, Validator, View;

class ResumeRepository extends RepositoryBase {

    public function fileUpload($file, $job_id, $status, $category_resume=null, $cvemail=false, $cover_letter_id=null, $supporting_docs_id=null, $is_applied_resume=0) {
        $userID = Auth::id();
        $resumeInfo = $this->resumeInfo($file);
        $resume_id = $this->cvID($file, $job_id, true, $status, $category_resume, $cvemail, $cover_letter_id, $supporting_docs_id, $is_applied_resume);
        $job_match = $resumeInfo['address_info'];
        $salary = $resumeInfo['personal_info']['salary'];
        $skills = $resumeInfo['skills'];
        $categories = $resumeInfo['industries'];
        $work_history = $resumeInfo['work_history'];
        $education = $resumeInfo['education_info'];
        foreach ($work_history as $history) {
            $skills[] = $history['job_title'];
        }
        if(isset($skills)){
            $this->postSkillMatch($skills,$resume_id,$userID);
        }
        if(!empty($work_history)){
            $this->postWorkHistory($work_history,$userID,$resume_id);
        }
        if(!empty($education)){
            $this->postEducation($education,$userID);
        }
        return $resume_id;
    }

    public function postWorkHistory($work_history, $userID, $resume_id) {
        DB::table('ins_user_workhistory_info')->where('candidate_id', '=', $userID)->delete();
        $this->inserWorkHistory($work_history,$userID,$resume_id);
    }
   
    public function inserWorkHistory($work_history, $userID, $resume_id) {
        DB::table('ins_predicted_skills')->where('candidate_id', '=', $userID)->delete();
        foreach($work_history as $history) {
            $work_history = new CandidateWorkHistoryInfo();
            $work_history->candidate_id = $userID;
            $work_history->company_name = isset($history['organisation'])?$history['organisation']:'NULL';
            $work_history->job_title = isset($history['job_title'])?$history['job_title']:'NULL';
            $work_history->start_date_year = isset($history['start_date'])?$history['start_date']:'NULL';
            $work_history->end_date_year = isset($history['end_date'])?$history['end_date']:'NULL';
            $work_history->save();
            $this->inserPredictedSkill($history['predictedSkills'],$userID,$resume_id);
        }
    }

    public function inserPredictedSkill($skills, $userID, $resume_id) {
        if(!empty($skills)){
            foreach($skills as $skill){
                $distance = $skill['distance'];
                $skill_match = DB::table('ins_skillmatch_names')->where('skill_name', '=', $skill['skill'])->first();
                if(isset($skill_match)){
                    $skill_match_name =  $skill_match->skill_name;
                    $skill_count = $skill_match->count;
                    $skill_id = $skill_match->id;
                    if($skill == $skill_match_name){
                        $count = $skill_count+1;
                        DB::table('ins_skillmatch_names')->where('skill_name', '=', $skill_match_name)->update(['count' => $count]);
                        $this->postCandidatePredictedSkill($userID,$skill_id,$resume_id,$distance);
                    }
                } else {
                    $skill_match_name = new SkillMatchNames();
                    $skill_match_name->skill_name =  $skill['skill'];
                    $skill_match_name->count =  0;
                    $skill_match_name->save();
                    $skill_id = $skill_match_name->id;
                    $this->postCandidatePredictedSkill($userID,$skill_id,$resume_id,$distance);
                }
            }
        }
    }

    public function postCandidatePredictedSkill($userID, $skill_id, $resume_id, $distance) {
        $predict_skill = new CandidatePredictedSkills();
        $predict_skill->candidate_id = $userID;
        $predict_skill->resume_id = $resume_id;
        $predict_skill->skill_id = $skill_id;
        $predict_skill->percentage =$distance;
        $predict_skill->save();
    }
  
    public function postEducation($education, $userID) {
        DB::table('ins_user_education_info')->where('candidate_id', '=', $userID)->delete();          
        $this->inserCandidateEducation($education,$userID);
    }

    public function inserCandidateEducation($education, $userID) {
        foreach($education as $educate){
            $education_info = new CandidateEducatioInfo();
            $education_info->candidate_id = $userID;
            $education_info->qualification = isset($educate['qualification'])?$educate['qualification']:'NULL';
            $education_info->institution = isset($educate['institution'])?$educate['institution']:'NULL';
            $education_info->start_date_year =isset($educate['start_date'])?$educate['start_date']:'NULL';
            $education_info->end_date_year = isset($educate['end_date'])?$educate['end_date']:'NULL';
            $education_info->save();
        }
    }
   
    public function resumeInfo($file) {
        $contentType =  $file->getClientMimeType();
        $options = array(
            'content_type'=>$contentType,
            'write_file'=>true,
            'destination'=>storage_path().'/uploads',
            'name'=>$file->getClientOriginalName()
        );
        $fileProxy = new FileProxy($file,'desktop',$options);
        $resRedact =new ResumeRedact();
        $res = $resRedact->clean($fileProxy);
        $resumeInfo = array();
        $resumeInfo['profile_id'] = $res->profile_id;
        $resumeInfo['personal_info']['personal_email'] = $res->getPersonalInfo('personal_email');
        $resumeInfo['personal_info']['phone_number'] = $res->getPersonalInfo('phone_number');
        $resumeInfo['personal_info']['first_name'] = $res->getPersonalInfo('first_name');
        $resumeInfo['personal_info']['last_name'] = $res->getPersonalInfo('last_name');
        $resumeInfo['personal_info']['full_name'] = $res->getPersonalInfo('full_name');
        $resumeInfo['personal_info']['salary'] = $res->getPersonalInfo('salary');
        $resumeInfo['personal_info']['industry'] = $res->getPersonalInfo('industry');
        $resumeInfo['education_info'] = $res->getHighestEducation();
        $resumeInfo['work_history'] = $res->work_history;
        $resumeInfo['skills'] = $res->skills;
        $resumeInfo['address_info'] = $res->address_info;
        $resumeInfo['extra_info'] = $res->extra_info;
        $resumeInfo['industries'] = $res->industries;
        return $resumeInfo;
    }

    public function postCategories($userID, $categories, $job_id) {
        foreach($categories as $category){
            $category = trim($category);
            $category_match = DB::table('ins_job_category')->where('category_name', '=', $category)->first();
            if(empty($category_match)){
                $category_id = $this->postCategoryName($category);
                $this->postCategory($category_id,$userID,$job_id);
            } else {
                $category_match_id = $category_match->id;
                $this->postCategory($category_match_id,$userID,$job_id);
            }
        }
    }

    public function postCategoryName($category) {
        $category_name = new CategoryNames();
        $category_name->category_name = $category;
        $category_name->save();
        $category_id = $category_name->id;
        return $category_id;
    }

    public function postCategory($category_id,$userID,$job_id) {
        $category_candidate = new JobMatchCandidateCategory();
        $category_candidate->candidate_id = $userID;
        $category_candidate->category_id = $category_id;
        $category_candidate->job_id = $job_id;
        $category_candidate->save();
        return 'true';
    }

    public function cvID($file, $job_id, $resumeupload=true, $status, $category_resume=null, $cvemail=false, $cover_letter_id=null , $supporting_docs_id=null, $is_applied_resume=0) {
        $r_status =$status;
        $userID = Auth::id();
        if($resumeupload){
            $d_filepath = storage_path('uploads')."/". $file->getClientOriginalName();
            $file_res = new FileProxy($d_filepath, 'file');
            $config = Config::get('aws');
            $config['bucket'] = $config['stagingbuckets']['careerone'];
            $s3 = ExternalFileFactory::create('S3');
            $s3->open($config);
            $rand = md5($file->getClientOriginalName().rand());
            $upload = $s3->upload(
                $file_res,
                'ins/resumes/' . $userID . '/' . $rand,
                ['object' => 1]
            );
            $extension = $file->getClientOriginalExtension();
            DB::table('ins_cv')->where('candidate_id', '=', $userID)->update(['is_latest'=>0]);
            $name = $file->getClientOriginalName();
            $resume = new Resumes();
            $resume->candidate_id = $userID;
            $resume->resume_url = $upload->get('ObjectURL');
            $resume->resume_name = $name;
            $resume->job_id = $job_id;
            $resume->is_latest = 1;
            $resume->status = $r_status;
            $resume->extension = $extension;
            $resume->category_id = $category_resume;
            $resume->is_applied_resume = $is_applied_resume;
            $resume->save();
            $resume_id = $resume->id;
            if($job_id != 0){
                $chkjob_id = $this->chkcandJob($job_id,$userID);
                if(empty($chkjob_id)){
                    $job_cand_id = new JobCandidateID();
                    $job_cand_id->job_id = $job_id;
                    $job_cand_id->candidate_id = $userID;
                    $job_cand_id->submit_status = $status;
                    $job_cand_id->save();
                } else {
                    $job_cand_update = JobCandidateID::find($chkjob_id->id);
                    $job_cand_update->submit_status = $status;
                    //ins_progress
                    $id = $chkjob_id->ins_job_apply_id;
                    $jobapp_service = new  UpdateJobApplied();
                    $jobapp_service->updateCRMJobAppliedINSProgress($id, $status=121660004);
                    $job_cand_update->ins_progress = 121660004;
                    $job_cand_update->ins_pushed = 'Y';
                    $job_cand_update->save();
                }
            }
            if($cvemail){
                $this->sendCVEmail($resume_id, $d_filepath, $userID, $cover_letter_id, $supporting_docs_id);
            } else {
                if($status == 1){
                    //Application submit
                    $this->sendAppliationSubmitEmail($resume_id, $d_filepath, $userID, $job_id, $cover_letter_id, $supporting_docs_id);
                } else if ($status == 0){
                    //Application Draft
                    $this->sendAppliationDraftEmail($resume_id, $d_filepath, $userID, $job_id, $cover_letter_id, $supporting_docs_id);
                }
            }
            return $resume_id;
        } else 
            $resume_id = $file;
            $url = DB::table('ins_cv')->where('id', '=', $resume_id)->select('ins_cv.*')->first();
            DB::table('ins_cv')->where('candidate_id', '=', $userID)->update(['is_latest'=>0]);
            if($job_id != 0){
                $chkjob_id = $this->chkcandJob($job_id,$userID);
                if(empty($chkjob_id)){
                   $job_cand_id = new JobCandidateID();
                    $job_cand_id->job_id = $job_id;
                    $job_cand_id->candidate_id = $userID;
                    $job_cand_id->submit_status = $status;
                    $job_cand_id->ins_progress = 0;
                    $job_cand_id->save();
                } else {
                    $job_cand_update = JobCandidateID::find($chkjob_id->id);
                    $job_cand_update->submit_status = $status;
                    //ins_progress
                    $id = $chkjob_id->ins_job_apply_id;
                    $jobapp_service = new  UpdateJobApplied();
                    $jobapp_service->updateCRMJobAppliedINSProgress($id,$status=121660004);
                    $job_cand_update->ins_progress = 121660004;
                    $job_cand_update->ins_pushed = 'Y';
                    $job_cand_update->save();
                    if($chkjob_id->ins_pushed == 'Y'){
                        $id = $chkjob_id->ins_job_apply_id;
                        $jobapp_service = new  UpdateJobApplied();
                        $jobapp_service->updateCRMJobApplied_new_progress($id,$status=100000005);
                    }
                }
            }
            $deleteCandidateJob = DB::table('ins_cv')->where('id', '=',$resume_id)->update(['job_id'=>$job_id]);
            $d_filepath = null;
            if($cvemail){
                $this->sendCVEmail($resume_id, $d_filepath, $userID, $cover_letter_id, $supporting_docs_id);
            } else {
                if($status == 1){
                    $jobapp_service = new UpdateJobApplied();
                    $jobapp_service->updateCRMJobApplied($id, 121660003, $resume_id, $cover_letter_id, $supporting_docs_id);
                    //Application submit
                    $this->sendAppliationSubmitEmail($resume_id, $d_filepath, $userID,$job_id, $cover_letter_id, $supporting_docs_id);
                } else if ($status == 0){
                    $jobapp_service = new UpdateJobApplied();
                    $jobapp_service->updateCRMJobApplied($id, 121660001, $resume_id, $cover_letter_id, $supporting_docs_id);
                    //Application Draft
                    $this->sendAppliationDraftEmail($resume_id, $d_filepath, $userID, $job_id, $cover_letter_id, $supporting_docs_id);
                }
            }
        }
    }

    public function sendCVEmail($resume_id, $file_path, $userID, $cover_letter_id = null, $supporting_docs_id = null) {
        $resume = DB::table('ins_cv')->where('id', '=', $resume_id)->first();
        $user = DB::table('users')->join('ins_employees','ins_employees.user_id','=','users.id')->where('users.id','=',$userID)->first();
        if(!empty($resume) && !empty($user)){
            $cat_name = '';
            if($resume->category_id ==  500 || $resume->category_id == 501){
                if($resume->category_id == 500 ){
                    $cat_name = 'Master';
                }
                if($resume->category_id == 501 ){
                    $cat_name = 'Draft';
                }
             } else {
                $category = DB::table('ins_job_category')->where('id','=',$resume->category_id)->first() ;
                if(!empty($category)){
                    $cat_name = $category->category_name;
                }
            }
            $emailService = new EmailRepository();
            $datail['name'] = $user->first_name . " " .$user->last_name ;
            $datail['email'] = $user->email;
            $datail['contact'] = $user->new_personalmobilenumber;
            $datail['othercontact'] = $user->new_personalhomenumber;
            $datail['resumecategory'] = $cat_name;
            $datail['resumelink'] = $resume->resume_url;
            $datail['first_name'] = $user->first_name;
            $message = \View::make('site/email/upload_resume_notification',array('datail' => $datail ))->render();
            $JobRepository = app()->make('App\Models\Repositories\JobRepository');
            $emailcs = $JobRepository->getcasemanagerEmail($user->ownerid);
            if(!empty($emailcs)){
                $to = $emailcs->internalemailaddress;
            } else {
                $to = Config::get('ins_emails.user_upload_cv_notification.to');
            }
            $subject = $datail['name'] . ' has uploaded a ' .  $cat_name . ' resume';
            $from = Config::get('ins_emails.user_upload_cv_notification.from');
            $attachments = array();
            if(!empty($file_path)) { 
                $attachments = array($file_path);
            }
            $emailService->sendAttachment(
                $to, $from, $subject, '', $message, $attachments, '', Config::get('ins_emails.user_upload_cv_notification.cc')
            );
        }
    }

    public function sendAppliationSubmitEmail($resume_id, $file_path, $userID, $job_id, $cover_letter_id=null , $supporting_docs_id=null) {
        $resume = DB::table('ins_cv')->where('id', '=', $resume_id)->first();
        $ins_covering_letter = DB::table('ins_covering_letter')->where('id', '=', $cover_letter_id)->first();
        $ins_supporting_doc = DB::table('ins_supporting_doc')->where('id', '=', $supporting_docs_id)->first();
        $job = DB::table('ins_jobs')->where('id', '=', $job_id)->first();
        $user = DB::table('users')->join('ins_employees','ins_employees.user_id','=','users.id')->where('users.id','=',$userID)->first();
        $casemanager = null;
        if(!empty($user)){
            $casemanager = DB::table('ins_system_users')->where('systemuserid','=',$user->ownerid)->first();
        }
        if(!empty($resume) && !empty($user)){
            $cat_name = '';
            if($resume->category_id == 500 || $resume->category_id == 501){
                if($resume->category_id == 500){
                    $cat_name = 'Master';
                }
                if($resume->category_id == 501){
                    $cat_name = 'Draft';
                }
             } else {
                $category = DB::table('ins_job_category')->where('id','=',$resume->category_id)->first();
                if(!empty($category)){
                    $cat_name = $category->category_name;
                }
            }
            $emailService = new EmailRepository();
            $datail['name'] = $user->first_name . " " .$user->last_name;
            $datail['email'] = $user->email;
            $datail['contact'] = $user->new_personalmobilenumber;
            $datail['othercontact'] = $user->new_personalhomenumber;
            $datail['resumecategory'] = $cat_name;
            $datail['resumelink'] = $resume->resume_url;
            $datail['jobtitle'] = $job->job_title;
            $datail['jobgrade'] = $job->job_grade;
            $datail['salarypackage'] = $job->salary_package;
            $datail['ins_covering_letter'] ='';
            $datail['ins_supporting_doc'] ='';
            if(!empty($ins_covering_letter)) { 
                $datail['ins_covering_letter'] = $ins_covering_letter->coveringletter_url;
            }
            if(!empty($ins_supporting_doc)){ 
                $datail['ins_supporting_doc'] = $ins_supporting_doc->url;
            }
            $message = \View::make('site/email/application-submit',array(
                'datail' => $datail,
                'case_manager' => !empty($user->employee) && !empty($user->employee->caseManager) ? $user->employee->caseManager : null
            ))->render();
            $subject = 'Application submitted for ' . $user->first_name;
            $to = Config::get('ins_emails.user_application_submit.to');
            $from = Config::get('ins_emails.user_application_submit.from');
            $attachments = array();
            if(!empty($file_path)) { 
                $attachments = array($file_path);
            }
            $emailService->sendAttachment($to, $from, $subject, '', $message, $attachments);
            //Case manager
            if(!empty($casemanager) ) {
                $casemanager_email = $casemanager->internalemailaddress ;
                $to = $casemanager_email;
                $emailService->sendAttachment($to, $from, $subject, '', $message, $attachments);
            }
        }
    }

    public function sendAppliationDraftEmail($resume_id, $file_path, $userID, $job_id, $cover_letter_id=null, $supporting_docs_id=null) {
        $resume = DB::table('ins_cv')->where('id','=',$resume_id)->first();
        $ins_covering_letter = DB::table('ins_covering_letter')->where('id','=',$cover_letter_id)->first();
        $ins_supporting_doc = DB::table('ins_supporting_doc')->where('id','=',$supporting_docs_id)->first();
        $job = DB::table('ins_jobs')->where('id','=',$job_id)->first();
        $user = DB::table('users')->join('ins_employees','ins_employees.user_id','=','users.id')->where('users.id','=',$userID)->first();
        $casemanager = null;
        if(!empty($user)){
            $casemanager = DB::table('ins_system_users')->where('systemuserid','=',$user->ownerid)->first();
        }
        if(!empty($resume) && !empty($user)){
            $cat_name = '';
            if($resume->category_id ==  500 || $resume->category_id == 501){
                if($resume->category_id == 500 ){
                    $cat_name = 'Master';
                }
                if($resume->category_id == 501 ){
                    $cat_name = 'Draft';
                }
             } else {
                $category = DB::table('ins_job_category')->where('id','=',$resume->category_id)->first();
                if(!empty($category)){
                    $cat_name = $category->category_name;
                }
            }
            $emailService = new EmailRepository();
            $datail['name'] = $user->first_name . " " .$user->last_name;
            $datail['email'] = $user->email;
            $datail['contact'] = $user->new_personalmobilenumber;
            $datail['othercontact'] = $user->new_personalhomenumber;
            $datail['resumecategory'] = $cat_name;
            $datail['resumelink'] = $resume->resume_url;
            $datail['jobtitle'] = $job->job_title;
            $datail['jobgrade'] = $job->job_grade;
            $datail['salarypackage'] = $job->salary_package;
            $datail['ins_covering_letter'] ='';
            $datail['ins_supporting_doc'] ='';
            if(!empty($ins_covering_letter)){ 
                $datail['ins_covering_letter'] = $ins_covering_letter->coveringletter_url;
            }
            if(!empty($ins_supporting_doc)){ 
                $datail['ins_supporting_doc'] = $ins_supporting_doc->url;
            }
            $message = \View::make('site/email/application-draft',array(
                'datail' => $datail,
                'case_manager' => !empty($user->employee) && !empty($user->employee->caseManager) ? $user->employee->caseManager : null
            ))->render();
            $subject = 'Application draft for ' . $user->first_name ;
            $to = Config::get('ins_emails.user_application_draft.to');
            $from = Config::get('ins_emails.user_application_draft.from');
            $attachments = array();
            if(!empty($file_path)){ 
                $attachments = array($file_path);
            }
            $emailService->sendAttachment($to, $from, $subject, '', $message, $attachments);
            //Case manager
            if(!empty($casemanager) ){
                $casemanager_email = $casemanager->internalemailaddress;
                $to = $casemanager_email;
                $emailService->sendAttachment($to, $from, $subject, '', $message, $attachments);
            }
        }
    }

    public function chkcandJob($job_id, $userID) {
        return DB::table('ins_job_candidate')->where('candidate_id','=',$userID)->where('job_id','=',$job_id)->select(['ins_job_candidate.*'])->first();
    }

	public function postSkillMatch($skills, $resume_id, $userID) {
        DB::table('ins_skill_candidate')->where('candidate_id', '=', $userID)->delete();
        foreach($skills as $skill){
            $skill_match = DB::table('ins_skillmatch_names')->where('skill_name', '=', $skill)->first();
            if(isset($skill_match)){
                $skill_match_name = $skill_match->skill_name;
                $skill_count = $skill_match->count;
                $skill_id = $skill_match->id;
                if($skill == $skill_match_name){
                    $count = $skill_count+1;
                    DB::table('ins_skillmatch_names')->where('skill_name', '=', $skill_match_name)->update(['count' => $count]);
                    $this->postCandidateSkill($userID,$skill_id,$resume_id);
                }
            } else {
                $skill_match_name = new SkillMatchNames();
                $skill_match_name->skill_name =  $skill;
                $skill_match_name->count =  0;
                $skill_match_name->save();
                $skill_id = $skill_match_name->id;
                $this->postCandidateSkill($userID,$skill_id,$resume_id);
           }
        }
    }

    public function postCandidateSkill($userID, $skill_id, $resume_id) {
        $candidate_skill = new CandidateSkillMatch();
        $candidate_skill->candidate_id = $userID;
        $candidate_skill->skill_id = $skill_id;
        $candidate_skill->resume_id = $resume_id;
        $candidate_skill->save();
    }

    public function postJobMatch($job_match, $salary, $resume_id, $userID, $job_id) {
        $jobmatch_candidate = new JobMatchCandidate();
        $jobmatch_candidate->candidate_id = $userID;
        $jobmatch_candidate->suburb = isset($job_match[0]['suburb'])?$job_match[0]['suburb']:'NULL';
        $jobmatch_candidate->state = isset($job_match[0]['state'])?$job_match[0]['state']:'NULL';
        $jobmatch_candidate->postcode =isset($job_match[0]['postcode'])?$job_match[0]['postcode']:'NULL';
        $jobmatch_candidate->resume_id = $resume_id;
        $jobmatch_candidate->job_id = $job_id;
        $jobmatch_candidate->save();
        $jobmatch_candidate_id = $jobmatch_candidate->id;
        $salary = $this->salaryCalc($salary,$jobmatch_candidate_id);
    }

    public function salaryCalc($salary, $jobmatch_candidate_id) {
        $result = preg_split('/[\s,-\/]+/', $salary);
        $salary_from = isset($result[0])? $result[0]:'';
        $salary_to = isset($result[1])? $result[1]:'';
        if(!empty($salary_to)){
        $salary_to = intval(preg_replace('/[^0-9]+/', '', $salary_to), 10);
            if(!$salary_to % 1000 == 0){
                $salary_to = $salary_to*1000;
                DB::table('ins_jobmatch_candidate')->where('id','=',$jobmatch_candidate_id)->update(['salary_to'=>$salary_to]);
            } else {
                DB::table('ins_jobmatch_candidate')->where('id','=',$jobmatch_candidate_id)->update(['salary_to'=>$salary_to]);
            }
        } else {
            DB::table('ins_jobmatch_candidate')->where('id','=',$jobmatch_candidate_id)->update(['salary_to'=>$salary_to]);
        }
        if(!empty($salary_from)){
        $salary_from = intval(preg_replace('/[^0-9]+/', '', $salary_from), 10);
            if(!$salary_from % 1000 == 0){
                $salary_from = $salary_from*1000;
                DB::table('ins_jobmatch_candidate')->where('id','=',$jobmatch_candidate_id)->update(['salary_from'=>$salary_from]);
            } else {
                 DB::table('ins_jobmatch_candidate')->where('id','=',$jobmatch_candidate_id)->update(['salary_from'=>$salary_from]);
            }
        } else {
            DB::table('ins_jobmatch_candidate')->where('id','=',$jobmatch_candidate_id)->update(['salary_from'=>$salary_from]);
        }
        return true;
    }

    public function coveringLetterUpload($covering_letter, $job_id) {
        $userID = Auth::id();
        $file_res = new FileProxy($covering_letter->getRealPath(), 'file');
        $config = Config::get('aws');
        $config['bucket'] = $config['stagingbuckets']['careerone'];
        $s3 = ExternalFileFactory::create('S3');
        $s3->open($config);
        $rand = md5($covering_letter->getClientOriginalName().rand());
        $upload = $s3->upload(
            $file_res,
            'ins/resumes/' . $userID . '/' . $rand,
            ['object' => 1]
        );
        $name = $covering_letter->getClientOriginalName();
        $extension = $covering_letter->getClientOriginalExtension();
        $coveringletter = new CoveringLetter();
        $coveringletter->candidate_id = $userID;
        $coveringletter->covering_letter_name = $name;
        $coveringletter->job_id = $job_id;
        $coveringletter->coveringletter_url = $upload->get('ObjectURL');
        $coveringletter->extension = $extension;
        $coveringletter->save();
        return $coveringletter->id;
    }

    public function supportingDocsUpload($supporting_file, $job_id) {
        $userID = Auth::id();
        $file_res = new FileProxy($supporting_file->getRealPath(), 'file');
        $config = Config::get('aws');
        $config['bucket'] = $config['stagingbuckets']['careerone'];
        $s3 = ExternalFileFactory::create('S3');
        $s3->open($config);
        $rand = md5($supporting_file->getClientOriginalName().rand());
        $upload = $s3->upload(
            $file_res,
            'ins/resumes/' . $userID . '/' . $rand,
            ['object' => 1]
        );
        $name = $supporting_file->getClientOriginalName();
        $extension = $supporting_file->getClientOriginalExtension();
        $supportingdoc = new SupportingDoc();
        $supportingdoc->candidate_id = $userID;
        $supportingdoc->name = $name;
        $supportingdoc->job_id = $job_id;
        $supportingdoc->url = $upload->get('ObjectURL');
        $supportingdoc->extension = $extension;
        $supportingdoc->save();
        return $supportingdoc->id;
    }
    
    public function downloadcvletter($id) {
        $userID = Auth::id();
        $url = $this->getcvletter($id);
        $resume_url=$url->resume_url;
        $resume_url = explode("/", $resume_url);
        $config = Config::get('aws');
        $config['bucket'] = $config['stagingbuckets']['careerone'];
        $s3 = ExternalFileFactory::create('S3');
        $s3->open($config);
        $destpath = storage_path().'/download/';
        $name = $url->resume_name;
        $stream = $s3->download($resume_url[7], $destpath,array('path' => 'ins/resumes/'.$userID.'/'),$name);
        $file = storage_path().'/download/'.$name;
        return Response::download($file);
    }

    public function downloadsupportpdf($id) {
        $userID = Auth::id();
        $url = $this->getsupportdoc($id);
        $resume_url=$url->resume_url;
        $resume_url = explode("/", $resume_url);
        $config = Config::get('aws');
        $config['bucket'] = $config['stagingbuckets']['careerone'];
        $s3 = ExternalFileFactory::create('S3');
        $s3->open($config);
        $destpath = storage_path().'/download/';
        $name = $url->resume_name;
        $stream = $s3->download($resume_url[7], $destpath,array('path' => 'ins/resumes/'.$userID.'/'),$name);
        $file = storage_path().'/download/'.$name;
        return Response::download($file);
    }

    public function downloadfile($id) {
        $userID = Auth::id();
        $url = $this->getResumeUrl($id);
        $resume_url=$url->resume_url;
        $resume_url = explode("/", $resume_url);
        $config = Config::get('aws');
        $config['bucket'] = $config['stagingbuckets']['careerone'];
        $s3 = ExternalFileFactory::create('S3');
        $s3->open($config);
        $destpath = storage_path().'/download/';
        $name = $url->resume_name;
        $stream = $s3->download($resume_url[7], $destpath, array('path' => 'ins/resumes/'.$userID.'/'), $name);
        $file = storage_path().'/download/'.$name;
        if (\File::exists($file)){
            $data['data'] = \Response::download($file);
            $data['status'] = true;;
            return $data;
        } else {
            $data['data'] = 'File does not exist';
            $data['status'] = false;;
            return $data;
        }
    }

    public function downloadpdf($id) {
        $userID = Auth::id();
        $url = $this->getPdfUrl($id);
        $pdf_url=$url->url;
        $pdf_url = explode("/", $pdf_url);
        $config = Config::get('aws');
        $config['bucket'] = $config['stagingbuckets']['careerone'];
        $s3 = ExternalFileFactory::create('S3');
        $s3->open($config);
        $destpath = storage_path().'/download/';
        $name = $url->name;
        $stream = $s3->download($pdf_url[7], $destpath,array('path' => 'ins/resumes/'.$userID.'/'),$name);
        $file = storage_path().'/download/'.$name;
        return Response::download($file);
    }

    public function getPdfUrl($id) {
        return DB::table('ins_pdf_usercapab')->where('id','=',$id)->select(['url','name'])->first();
    }

    public function getResumeUrl($id) {
        return DB::table('ins_cv')->where('id','=',$id)->select(['resume_url','resume_name'])->first();
    }

    public function getcvletter($id) {
        return DB::table('ins_covering_letter')->where('id','=',$id)->select(['coveringletter_url','covering_letter_name'])->first();
    }

    public function getsupportdoc($id) {
        return DB::table('ins_supporting_doc')->where('id','=',$id)->select(['url','name'])->first();
    }

    public function uploadUserCapab($file, $userID) {
        $file_res = new FileProxy($file->getRealPath(), 'file');
        $config = Config::get('aws');
        $config['bucket'] = $config['stagingbuckets']['careerone'];
        $s3 = ExternalFileFactory::create('S3');
        $s3->open($config);
        $rand = md5($file->getClientOriginalName().rand());
        $upload = $s3->upload(
            $file_res,
            'ins/resumes/' . $userID . '/' . $rand,
            ['object' => 1]
        );
        $extension = $file->getClientOriginalExtension();
        DB::table('ins_pdf_usercapab')->where('user_id','=',$userID)->update(['is_latest'=> 0]);
        $name = $file->getClientOriginalName();
        $userPDF = new UserPDFCapability();
        $userPDF->user_id = $userID;
        $userPDF->url = $upload->get('ObjectURL');
        $userPDF->name = $name;
        $userPDF->is_latest = 1;
        $userPDF->extension = $extension;
        $userPDF->save();
    }

    public function storeResume($url, $name, $userID, $destpath) {
        $resume_url = explode("/", $url);
        $config = Config::get('aws');
        $config['bucket'] = $config['stagingbuckets']['careerone'];
        $s3 = ExternalFileFactory::create('S3');
        $s3->open($config);
        $stream = $s3->download($resume_url[7], $destpath,array('path' => 'ins/resumes/'.$userID.'/'),$name);
        $file = $destpath.$name;
        return Response::download($file);
    }

    public function upoadRole_desc($file, $job_id) {
        $file_res = new FileProxy($file->getRealPath(), 'file');
        $config = Config::get('aws');
        $config['bucket'] = $config['stagingbuckets']['careerone'];
        $s3 = ExternalFileFactory::create('S3');
        $s3->open($config);
        $rand = md5($file->getClientOriginalName().rand());
        $upload = $s3->upload(
            $file_res,
            'ins/role_description/' . $job_id . '/' . $rand,
            ['object' => 1]
        );
        $extension = $file->getClientOriginalExtension();
        DB::table('ins_role_desc_pdf')->where('job_id','=',$job_id)->update(['is_latest'=> 0]);
        $name = $file->getClientOriginalName();
        $userPDF = new JobRoleDescriprion();
        $userPDF->job_id = $job_id;
        $userPDF->url = $upload->get('ObjectURL');
        $userPDF->name = $name;
        $userPDF->is_latest = 1;
        $userPDF->extension = $extension;
        $userPDF->save();
    }

    public function download_role_desc($role_desc_id) {
        $url = DB::table('ins_role_desc_pdf')->where('id','=',$role_desc_id)->select(['url','name'])->first();
        $pdf_url=$url->url;
        $pdf_url = explode("/", $pdf_url);
        $job_id = $url->job_id;
        $config = Config::get('aws');
        $config['bucket'] = $config['stagingbuckets']['careerone'];
        $s3 = ExternalFileFactory::create('S3');
        $s3->open($config);
        $destpath = storage_path().'/download/';
        $name = $url->name;
        $stream = $s3->download($pdf_url[7], $destpath,array('path'=>'ins/role_description/'.$job_id.'/'),$name);
        $file = storage_path().'/download_role_desc/'.$name;
        return Response::download($file);
    }

    public function getCategoryResume($userId, $categoryId) {
        return DB::table('ins_cv')
            ->where('candidate_id', '=', $userId)
            ->where('category_id', '=', $categoryId)
            ->orderBy('created_at', 'DESC')
            ->first();
    }
}
