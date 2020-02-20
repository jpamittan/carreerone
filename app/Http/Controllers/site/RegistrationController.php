<?php

namespace App\Http\Controllers\site;

use App\Http\Controllers\Controller;
use App\Models\Services\UserService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class RegistrationController extends Controller {
	private $userService;
	
	function __construct(UserService $userService) {
		$this->userService = $userService;
	}
	
	public function confirm($token) {
		$user = $this->userService->verifyActivationToken($token);
		if ($user) {
			Session::put('user_id',$user->id);
		} else {
			return Redirect::to(URL::route('site-login'));
		}
		return View::make(
			'site.user.user_activation',
			array(
            	'user' => $user,
            	'case_manager' => !empty($user->employee) && !empty($user->employee->caseManager) ? $user->employee->caseManager : null
            )
		);
	}
	
	public function activate() {
		$data = Input::all();
		$data["user_id"] = Session::get('user_id');
		$user = $this->userService->activateAccount($data);
		return Redirect::to(URL::route('site-login'))->with(['activated'=>true]);
	}
}
