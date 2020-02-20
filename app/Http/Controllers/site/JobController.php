<?php

namespace App\Http\Controllers\site;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\site\AdminController;
use App\Models\Services\JobAssetService;
use App\Models\Entities\Jobs;
use App\Models\Services\Purifier;
use App\Models\Repositories\JobRepository;
use App\Models\Repositories\ResumeRepository;
use App\Models\Services\PushJobmatchStatus;
use View, Redirect, Response;

class JobController extends AdminController {

  public function jobMatch() {
    $service = new JobAssetService();
    $job_match = $service->JobMatchCandidate();
    return $job_match;
  }

  public function getJob($job_id) {
    $checkprofile =  $this->checkProfile();
    $checkskills = $this->checkSkills();
    if (!$checkprofile)
      $msg = $checkprofile;
      return Redirect::to('site/profile')->with('error', $msg);
    }
    if ($checkskills < 10) {
      if (10 - $checkskills == 1) {
        $msg = 'You must have a minimum of 10 skills defined. Please add 1 more skill to Skill Summary before continuing.';
      } else {
        $msg = 'You must have a minimum of 10 skills defined. Please add ' . (10 - $checkskills) . ' more skills to Skill Summary before continuing.';
      }
      return Redirect::to('site/profile')->with('error', $msg);
    }
    $service = new JobAssetService();
    $getJob = $service->getJob($job_id);
    $isjobeoi = $service->isjobeoi($job_id);
    $isjobeoiapply = $service->isjobeoiapply($job_id);
    $isjobeoirejected = $service->isjobeoirejected($job_id);
    $isjobapplied = $service->isjobapplied($job_id);
    $isjobdraft = 0;
    $progressstatus = 0;
    $jobprogressstatus = $service->jobProgressStatus($job_id);
    if (!empty($jobprogressstatus)) {
      $progressstatus = $jobprogressstatus->ins_progress;
    }
    if (isset($getJob)) {
      $getJob->job_draft = $isjobdraft;
      $getJob->job_apply = $isjobapplied;
      $getJob->job_eoi = $isjobeoi;
      $getJob->isjobeoiapply = $isjobeoiapply;
      $getJob->job_eoi_rejected = $isjobeoirejected;
      $getJob->jobprogressstatus = $progressstatus;
      $desc = preg_replace('/\r?\n|\r/','<br/>', $getJob->position_description);
      $resumeRepo = new ResumeRepository();
      if (!$resume = $resumeRepo->getCategoryResume(Auth::id(), $getJob->job_category_id)) {
        $resume = $resumeRepo->getCategoryResume(Auth::id(), 500);
      }
      if ($resume && !$resume->uploaded_to_monster) {
        $service->uploadToMonster($resume);
      } else if ($resume && $resume->uploaded_to_monster) {
        $getskills = $service->checkMonsterSkills($job_id, $resume);
      }
      if (!empty($getskills)) {
        $getskills = $service->getskills($job_id, $resume->id);
      }
      $getskills= null;
      if (!empty($resume)) {
        $getskills = $service->getskills($job_id, $resume->id);
      }   
      $getcapabilities = $service->getCapabilities($job_id);
      $capabilities = [];
      foreach ($getcapabilities as $cap) {
        $capabilities[$cap->group_name][] = [
          'core_status' => $cap->core_status,
          'image' => $cap->group_images,
          'capabilities' => $cap->match_names,
          'level' => $cap->level_name, 
          'score' => $cap->score
        ];
      }
      $keywords = $service->countKeywords($getJob->position_description);
      $saveKeywords = $service->saveKeywords($getJob->id, $keywords);
      return View::make('site.home.jobview', array(
        'job' => $getJob,
        'skill_match' => $getskills,
        'capabilities' => $capabilities,
        'keywords' => $keywords,
        'description'=> $desc
      ));
    } else {
      return Redirect::route('site-nojob');
    }
  }

  public function jobView($job_id) {
    $checkprofile = $this->checkProfile() ;
    $checkskills = $this->checkSkills();
    if(!( $checkprofile)){
      $msg = $msg =trans('messages.profile.main');;
      return Redirect::to('site/profile')->with('error', $msg);
    }
    if ($checkskills < 10) {
      if (10 - $checkskills == 1) {
        $msg = 'You must have a minimum of 10 skills defined. Please add 1 more skill to Skill Summary before continuing.';
      } else {
        $msg = 'You must have a minimum of 10 skills defined. Please add ' . (10 - $checkskills) . ' more skills to Skill Summary before continuing.';
      }
      return Redirect::to('site/profile')->with('error', $msg);
    }
    $service = new JobAssetService();
    $getJob = $service->getJob($job_id);
    $isjobeoi = $service->isjobeoi($job_id);
    $isjobeoiapply = $service->isjobeoiapply($job_id);
    $isjobeoirejected = $service->isjobeoirejected($job_id);
    $isjobapplied = $service->isjobapplied($job_id);
    $isjobdraft = 0;
    $progressstatus=0;
    $jobprogressstatus = $service->jobProgressStatus($job_id);
    if (!empty($jobprogressstatus)) {
      $progressstatus=$jobprogressstatus->ins_progress;
    }
    if(isset($getJob)) {
      $getJob->job_draft = $isjobdraft;
      $getJob->job_apply = $isjobapplied;
      $getJob->job_eoi= $isjobeoi;
      $getJob->isjobeoiapply= $isjobeoiapply;
      $getJob->job_eoi_rejected= $isjobeoirejected;
      $getJob->jobprogressstatus= $progressstatus;
      $desc =  preg_replace('/\r?\n|\r/','<br/>', $getJob->position_description);
      $getskills =  $service->getskills($job_id);
      $getcapabilities =  $service->getCapabilities($job_id);
      $capabilities = [];
      foreach ($getcapabilities as $cap) {
        $capabilities[$cap->group_name][] = [
          'core_status' => $cap->core_status,
          'image' => $cap->group_images,
          'capabilities' => $cap->match_names,
          'level' => $cap->level_name,
          'score' => $cap->score
        ];
      }
      $keywords = $service->countKeywords($getJob->position_description);
      $saveKeywords = $service->saveKeywords($getJob->id, $keywords);
      return View::make(
        'site.home.jobview-future',
        array(
          'job' => $getJob,
          'skill_match' =>$getskills,
          'capabilities' =>$capabilities,
          'keywords' =>$keywords,
          'description'=>$desc
        )
      );
    } else {
      return Redirect::route('site-nojob');
    }
  }

  public function jobApply($job_id) {
    $checkprofile = $this->checkProfile() ;
    $checkskills = $this->checkSkills();
    if(!$checkprofile){
      $msg = $msg =trans('messages.profile.main');;
      return Redirect::to('site/profile')->with('error', $msg);
    }  
    if ($checkskills < 10) {
      if (10 - $checkskills == 1) {
        $msg = 'You must have a minimum of 10 skills defined. Please add 1 more skill to Skill Summary before continuing.';
      } else {
        $msg = 'You must have a minimum of 10 skills defined. Please add ' . (10 - $checkskills) . ' more skills to Skill Summary before continuing.';
      }
      return Redirect::to('site/profile')->with('error', $msg);
    }
    $service = new JobAssetService();
    $getJob = $service->getJob($job_id);
    $isjobeoi = $service->isjobeoi($job_id);
    $isjobeoiapply = $service->isjobeoiapply($job_id);
    $isjobeoirejected = $service->isjobeoirejected($job_id);
    $isjobapplied = $service->isjobapplied($job_id);
    $isjobdraft = 0;
    $isjobfromdraft = $service->isjobfromdraft($job_id);
    $get_resume = $service->getResume();
    if (isset($getJob)) {
      $getJob->job_draft = $isjobdraft;
      $getJob->isjobfromdraft = $isjobfromdraft;
      $getJob->job_apply = $isjobapplied;
      $getJob->job_eoi= $isjobeoi;
      $getJob->isjobeoiapply= $isjobeoiapply;
      $getJob->job_eoi_rejected= $isjobeoirejected;
      return View::make(
        'site.home.apply',
        array(
          'job' => $getJob,
          'resume' => $get_resume,
        )
      );
    } else {
      return Redirect::route('site-nojob');
    }
  }

  public function jobApplyEoi($job_id) {
    $service = new JobAssetService();
    $jobid = Jobs::findByCrmid($job_id);
    $job = Jobs::find($job_id);
    $user = \DB::table('users')
            ->where('id', '=', Auth::id())
            ->select('crm_user_id')
            ->first();
    $jobmatch = \DB::table('ins_jobmatch')
                ->where('candidate_id','=', Auth::id())
                ->where('job_id', '=', $job->id)
                ->first();
    if (!empty($jobmatch)) {
      $jobmatchstatus = new PushJobmatchStatus();
      $fields = array(
        ['name'=>'ins_matchstatus', 'value' => 121660005 , 'type'=> 'option']
      );
      $jobmatchstatus->pushJobMatchStatusIndividualMultiple(
        $jobmatch->new_jobmatchedid,
        $fields
      );
      \DB::table('ins_jobmatch')
      ->where('id','=',  $jobmatch->id)
      ->update(['match_status' => 121660005]);
    }
    return Response::json(
      ['success' => true]
    );
  }

  public function jobRejectedEoi() {
    $input = Input::all();
    $job_id= $input['job_id'];;
    $comments= $input['imnotinterested_txt'];;
    $service = new JobAssetService();
    $jobid = Jobs::findByCrmid($job_id);
    $job  = Jobs::find($job_id);
    $user = \DB::table('users')
            ->where('id','=', Auth::id())
            ->select('crm_user_id')
            ->first();
    $jobmatch = \DB::table('ins_jobmatch')
                ->where('candidate_id','=', Auth::id())
                ->where('job_id','=', $job->id)
                ->first();
    if (!empty($jobmatch)) {
      $jobmatchstatus = new PushJobmatchStatus();
      $fields = array(
        ['name'=>'ins_matchstatus', 'value' => 121660006 , 'type'=> 'option'],
        ['name'=>'new_comment', 'value' => $comments,  'type'=> 'string'],
      );
      $jobmatchstatus->pushJobMatchStatusIndividualMultiple(
        $jobmatch->new_jobmatchedid,
        $fields
      );
      \DB::table('ins_jobmatch')
      ->where('id', '=',  $jobmatch->id)
      ->update(['match_status' => 121660006, 'comments'=>$comments]);
    }
    return Response::json(
      ['success' => true]
    );
  }

  public function jobReject($job_id) {
    $service = new JobAssetService();
    $rejectJob = $service->rejectJob($job_id);
    return Redirect::to(
      'site/dashboard'
    )
    ->with('error', trans('messages.apply.rejectjob'));
  }

  public function noJobs() {
    return View::make('site.home.nojobs');
  }

  public function skillMatch() {
    $service = new JobAssetService();
    $skill_match = $service->SkillMatchCandidate();
    return $skill_match;
  }

  public function candidateInterviewDates(){
    $service = new JobAssetService();
    $get_dates = $service->getCandidateInterviewDates();
    if (!empty($get_dates)) {
      return Response::json(['success' => true, 'interview_dates' => $get_dates]);
    } else {
      return Response::json(['success' => false]);
    }
  }

  public function deleteJob() {
    $input = Input::all();
    $service = new JobAssetService();
    $job_id =  $input['job_id'];
    $delete_job = $service->deleteCandidateJob($job_id);
    return Response::json(
      ['success' => true]
    );
  }

  public function candidatePendingDates() {
    $service = new JobAssetService();
    $get_dates = $service->getCandidatePendingDates();
    return Response::json(['success' => true, 'interview_pending_dates' => $get_dates]);
  }


  public function acceptInterview(){
    $input = Input::all();
    if ($input['interview_id'] == '') {
      return Response::json(
        [
          'success' => false,
          'message' => 'Select Timings to Confirm the Interview'
        ]
      );
    } else {
      $service = new JobAssetService();
      $accept_interview = $service->postAcceptInterview($input);
      if ($accept_interview == 'true') {
        return Response::json(
          [
            'success' => true,
           'message' => 'Successfully Confirmed the Sheduled Interview'
          ]
        );
      } else {
        return Response::json(
          [
            'success' => false,
            'message' => 'You Have Interview on Same Time, Please select different time or Reject'
          ]
        );
      }
    }
  }

  public function rejectInterview() {
    $input = Input::all();
    $service = new JobAssetService();
    $reject_interview = $service->postRejectInterview($input);
    return Response::json(
      ['success' => true, 'message' => 'Rejected Interview']
    );
  }

  public function pendingInterviewConfirm($id) {
    $service = new JobAssetService();
    $job_detail = $service->getCandidateInterviewJobDetails($id);
    if(isset($job_detail)){
      $job_details = $job_detail['job_details'];
      $job_candidate = $job_detail['job_candidate'];
      return View(
        'site.home.candidate-calandar',
        array(
          'job_details' => $job_details,
          'job_candidate'=>$job_candidate
        )
      );
    } else {
      return View('site.home.no-pending-interview');
    }
  }

  public function candidateCalandarPendingDates($id) {
    $service = new JobAssetService();
    $dates = $service->candidateCalandarPendingDates($id);
    return Response::json(
      ['success' => true, 'pending_dates' => $dates]
    );
  }

  public function location($keywords) {
    $service = new JobAssetService();
    $location = $service->getLocation($keywords);
    return Response::json(
      ['location' => $location]
    );
  }

  public function category($keywords) {
    $service = new JobAssetService();
    $category = $service->getCategory($keywords);
    return Response::json(
      ['category' => $category]
    );
  }

  public function downloadRD($id){
    $resumeRep = new JobRepository();
    $data = $resumeRep->downloadfileWithError($id);
    if ($data['status']) {
      return $data['data'];
    } else {
      return $data['data'];;
    }  
  }
}
