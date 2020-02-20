<?php

namespace App\Models\Services;

use App\Models\Repositories\EmailVerifiedRepository;
use App\Models\Gateways\Email\AWSEmail;
use View, Config;

class EmailVerifiedService {
	private $EmailVerifiedRepository;

	function __construct(){
		$this->EmailVerifiedRepository = new EmailVerifiedRepository();
	}

	function verifiedEmail($email){
		$results = $this->EmailVerifiedRepository->verifiedEmail($email);
		$arr_return = array();
		$arr_return['returns'] = false;
		$arr_return['is_active'] = false;
		if(!empty($results)) {
 			if($results->is_active == 1) {
 				$token = date('U', strtotime('+1 day'));
	 			$arr_return = array(
					'returns'	=> true,
					'id'		=> $results->id,
					'email'		=> $results->email,
					'name'	=> $results->first_name,
					'is_active' => $results->is_active,
					'expire_in' => date('d/m/Y h:i:s A', $token),
					'token'		=> str_replace('=','',base64_encode($token.'-'.$results->email))
				);
	 			$save = $this->EmailVerifiedRepository->savePasswordResets($arr_return);
	 			if($save) {
	 				$from 			= Config::get('ins_emails.email_verify.from');
					$to 			= $arr_return['email'];
					$caseManagerMatchRepository = app()->make('App\Models\Repositories\CaseManagerMatchRepository');
				    $case_m = $caseManagerMatchRepository->getCMDetailsinfo($results->ownerid);
					$message 		= View::make('site/email/forgot-password',array(
					    'details'=>$arr_return, 'case_m' =>$case_m,
                        'case_manager' => !empty($results->employee) && !empty($results->employee->caseManager) ? $results->employee->caseManager : null
                    ))->render();
					$subject 		= 'Forgot Password';
					$email_gateway 	= new AWSEmail();
			        $email_gateway->send($to, $from, $subject, $message, array());
	 			}
 			} else {
 				$arr_return['is_active'] = true;
 			}
 		}
 		return $arr_return;
 	}

 	function resetPassword($inputs){
 		$results = $this->EmailVerifiedRepository->changePassword($inputs);
 		if($results['returns']) {
 			$from 			= Config::get('ins_emails.reset_password.from');
			$to 			= $inputs['email'];
			$message 		= View::make('site/email/change-password',array('details'=>$results))->render();
			$subject 		= 'Successfully Change Password';
			$email_gateway 	= new AWSEmail();
	        $email_gateway->send($to, $from, $subject, $message, array());
 		}
 		return $results;
 	}
	
}