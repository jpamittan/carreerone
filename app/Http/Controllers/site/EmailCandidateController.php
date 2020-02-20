<?php

namespace App\Http\Controllers\site;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\site\AdminController;
use App\Models\Services\EmailCandidateService;
use View, Redirect, Response;

class EmailCandidateController extends AdminController {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function emailCandidate() {
      $service = new EmailCandidateService();
      $service->emailCandidate();
    }

    public function interviewReminderEmailCandidate(){
        $service = new EmailCandidateService();
        $service->interviewReminderEmailCandidate();
    }

    public function interviewFeedback($crm_user_id, $id){
        $user = \DB::table('users')->where("crm_user_id",'=',$crm_user_id)->first();
        $ins_interviews_calandar = \DB::table('ins_interviews_calandar')
                                    ->where("candidate_id",'=',$user->id)
                                    ->where("id",'=',$id)
                                    ->where("feedback",'!=','')
                                    ->first();
        $ins_interviews_calandar_validate = \DB::table('ins_interviews_calandar')
                                            ->where("candidate_id",'=',$user->id)
                                            ->where("id",'=',$id)
                                            ->first();
        if (!empty($ins_interviews_calandar_validate)) {
            if (!empty($ins_interviews_calandar)) {
                return View::make(
                    'site.home.tell-us-how-you-went',
                    [
                        'crm_user_id' => $crm_user_id,
                        'id' => $id,
                        'submitted' => true
                    ]
                );
            } else {
                return View::make(
                    'site.home.tell-us-how-you-went',
                    [
                        'crm_user_id' => $crm_user_id,
                        'id' => $id,
                        'submitted' => false
                    ]
                );
            } 
        } else {
            return View::make(
                'site.home.error',
                ['message'=>'You do not have any feedback']
            );
        }
    }

    public function sendFeedback() {
        $inputs = Input::all();
        $service = new EmailCandidateService();
        $results = $service->sendFeedback($inputs);
        if($results) {
            $message = "Thank you for submitting your feedback.";
            $type    = 'alert-success';
        } else {
            $message = "Error, Failed to submit your feedback please try again!";
            $type = 'alert-danger';
        }
        return redirect()->route(
            'tell-us-how-you-went',
            [
                'crm_user_id' => $inputs['crm_id'],
                'id' => base64_decode($inputs['id'].'=')
            ]
        )
        ->with(['message'=> $message])
        ->with(['alertType'=>$type]);
    }
}
