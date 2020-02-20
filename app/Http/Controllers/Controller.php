<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\Models\Repositories\EmailRepository;
use Illuminate\Support\Facades\Auth;
use DB, View, Config, AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

class Controller extends BaseController {
    public function checkProfile() {
    	$userID = Auth::id();
    	$user = DB::table('users')
                ->join('ins_employees' ,'ins_employees.user_id' , '=','users.id')
                ->where('users.id','=',$userID)->first();
        if (!empty($user)) {
    		$location = DB::table('ins_user_job_locations')
    		 ->where('user_id','=',$userID)->count() ;
    		if ( $location <= 0) {
                return \trans('messages.profile.location');
    		}
    		$cat = DB::table('ins_user_job_category_types')
        		   ->where('user_id','=',$userID)->count() ;
    		if ($cat <= 0) {
    			return \trans('messages.profile.category');
    		}
    		if (empty($user->new_personalhomenumber)) {
    			if (empty($user->new_personalmobilenumber)) {
    				return \trans('messages.profile.phone');
    			}
    		}
            $skills = DB::table('ins_user_skill_assesment')
                    ->where('candidate_id','=',$userID)->count();
            if ($skills < 10) {
                return \trans('messages.profile.skill');
            }
            if ($user->profile_completion_email == 0) {
                $emailService = new EmailRepository();
                $message = View::make(
                    'site/email/profile-completion',
                    compact('user', 'data')
                )->render();
                $from = Config::get('ins_emails.profile_completion.from');
                $JobRepository = app()->make('App\Models\Repositories\JobRepository');
                $emailcs = $JobRepository->getcasemanagerEmail($user->ownerid);
                if (!empty($emailcs)) {
                     $to = $emailcs->internalemailaddress;
                } else {
                    $to = Config::get('ins_emails.profile_completion.to');
                }
                $subject = Config::get('ins_emails.profile_completion.subject');
                $emailService->send($message, $subject, $from, $to);
                if (!empty($user) && !empty($user->crm_user_id)) {
                    try {
                        app('App\Models\Repositories\UserPushRepository')
                        ->pushEmployeeIndividual(
                            $user->crm_user_id,
                            [
                                [
                                    'name' => 'new_skillsauditcompletedate',
                                    'value' => date('Y-m-d').'T'.date('h:i:s'),
                                    'type' => 'dateTime'
                                ]
                            ]
                        );
                    } catch (\Exception $exception) {
                        logger('Error while updating new_skillsauditcompletedate 
                        for user with crm_id ' . $user->crm_user_id);
                        logger('Exception:' . $exception->getMessage());
                    }
                }
            }
        	$u = \DB::table('users')
                ->where('id', $userID )
                ->update(['dashboard_profile' => 0, 'profile_completion_email' => 1]);
        		return true;
        }
        return false;
    }

    public function checkSkills() {
        return DB::table('ins_user_skill_assesment')
            ->where('candidate_id', '=', Auth::id())
            ->where('active', '=', 1)
            ->count();
    }

    public function checkPopupCateogry() {  
        $checkprofile = $this->checkProfile();
        if (!($checkprofile)) {
            return 0;
        }
        $userID = Auth::id();
        $user = DB::table('users')
                ->where('users.id', '=', $userID)
                ->first();
        if (!empty($user)) {
            if ($user->dashboard_profile == 0) {
                return 1;
            } else {
                return 0;
            }
        }
        return  1;
    }
}
