<?php

namespace App\Http\Controllers\site;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\site\AdminController;
use App\Models\Entities\Jobs;
use App\Models\Services\ScheduleAssetService;
use App\Models\Services\JobAssetService;
use View, Redirect, Response;

class ScheduleInterviewController extends AdminController {
    public function getCandiatesInterview($job_id) {
        $job_id = Jobs::findByCrmJobid($job_id);
        if(!empty($job_id)){
            $job_id = $job_id->id;
            $service = new ScheduleAssetService();
            $get_candidate_applied = $service->getAppliedCandidates($job_id);
            $canddates_scheduled = $service->getCandidatesSceduled($job_id);
            $job_det = $service->getJobDetails($job_id);
            if(!empty($job_det)){
                return View('site.home.calandar',array(
                    'candidates' => $get_candidate_applied,
                    'job' => $job_det,
                    'canddates_scheduled' => $canddates_scheduled
                ));
            } else {
                return View('site.home.error',array('message' =>'There is some error happened with job detail or cadidate information . Please contact administrator'));
            }
        } else {
            $message = 'No Interview Found To Schedule';
            return View('site.home.error',array('message' => $message));
        }
    }

    public function getCandidateSeletedDates($job_id) {
        $service = new ScheduleAssetService();
        $job_id = Jobs::findByCrmJobid($job_id);
        $job_assigned_dates = $service->getJobAssignedDates($job_id->id);
        if(count($job_assigned_dates)>0) {
            return Response::json(['success'=>true,'assigned_dates'=>$job_assigned_dates,'time'=>$job_assigned_dates[0]->time]);
        } else {
            return Response::json(['success'=>true,'assigned_dates'=>[],'time'=>'']);
        }
    }

    public function scheduleInterview() {
        $service = new ScheduleAssetService();
        $schedule = $service->scheduleInterview();
    }

    public function postCandiatesInterview() {
        $input = Input::all();
        $candidates  ='';
        if(isset($input['candidates'])  ){
            $candidates = json_decode($input['candidates']);
        }
        if(isset($candidates) && count($candidates) > 0){
            if(empty(json_decode($input['date_timings']))){
                return Response::json(
                    ['success' => false,
                    'message' => 'Select Timings to Schedule the Interview']
                );
            } elseif(empty(json_decode($input['candidates']))) {
                return Response::json(
                    [
                        'success' => false,
                        'message' => 'Select at least One Candidate to Schedule the Interview'
                    ]
                );
            } else {
               $service = new ScheduleAssetService();
               $schedule = $service->postScheduleInterview($input);
               return Response::json(['success' => true] );
           }
        } else {
            $service = new ScheduleAssetService();
            $schedule = $service->postScheduleInterview($input);
            return Response::json(['success' => true] );
        }
    }

    public function postCandiatesInterviewScreening() {
        $input = Input::all();
        if(empty(json_decode($input['candidates']))) {
            return Response::json(
                [
                    'success' => false,
                    'message' => 'Select at least One Candidate for screening'
                ]
            );
        } else {
           $service = new ScheduleAssetService();
           $schedule = $service->postScheduleInterviewScreening($input);
           return Response::json(['success' => true] );
       }
    }

    public function scheduledSucess() {
        return View::make('site.home.interviewSuccess');
    }

    public function screenedSucess() {
        return View::make('site.home.interviewSuccessScreened');
    }

    public function recruiterEmailView(){
        $this->repo = app()->make('App\Models\Repositories\ScheduleRepository');
        $job_ids =$this->repo->getJobID();
        if(!empty($job_ids)){
            foreach($job_ids as $job_id){
                $details = $this->repo->getAppliedCandidates($job_id->id);
                return View::make(
                    'site.email.recruiter-schedule',
                    array('details' => $details)
                );
            }
        }
    }
}
