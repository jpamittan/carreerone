<?php

namespace App\Http\Controllers\site;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Models\Entities\Role;
use App\Models\Entities\Permission;
use App\Models\Entities\User;
use App\Models\Repositories\AdminRepository;
use App\Models\Services\JobAssetService;
use App\Models\Services\EmailVerifiedService;
use Cache, Config, Cookie, Constants, LogProxy, Redirect, Request, Response, Session, URL, Validator, View;

class AuthController extends Controller {
    protected $validation = [
        'create' => [
            'email' => 'required|email',
            'password' => 'sometimes|required'
        ]
    ];

    public function index() {
        $userID = Auth::id();
        if (!empty( $userID)) {
            $flag = \DB::table('users')->find($userID);
            if (!empty($flag)) {
                return Redirect::route('site.home.dashboard');
            } else {
                return View('site.home.login');
            }
        } else {
            return View('site.home.login');
        }
    }

    /**
     * @param LoginRequest $loginRequest
     * @return mixed
     */
    public function login(LoginRequest $loginRequest) {
        $credentials = $loginRequest->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return Redirect::back()->withMessage('Please Enter Valid Email & Password');
        }
        $userID = Auth::id();
        $adminRepository = new AdminRepository();
        $userRoles = $adminRepository->getRoles($userID);
        if (in_array(3, $userRoles)) {
            $dashboard_profile = Auth::user()->dashboard_profile;
            if ($dashboard_profile == 0) {
                Session::put('dashboard_profile', 0);
                if (!empty(Session::get('url.intended'))) {
                    return Redirect::to(Session::get('url.intended'));
                }
                return Redirect::route('site.home.dashboard');
            } elseif ($dashboard_profile == 1) {
                Session::put('dashboard_profile', 1);
                return Redirect::route('site.home.profile');
            }
        }
        Auth::logout();
        return Redirect::back()->withMessage('You Don\'t Have Permission to Login, Please Check with your Manager');
    }
    
    public function forgotPassword() {
        $userID = Auth::id();
        $inputs = Input::all();
        $message = '';
        $alertType = '';
        if(!empty($inputs)) {
            $rules = array('email' => 'Required|email');
            $validator = Validator::make($inputs, $rules);
            if($validator->fails()) {
                $message =  $validator->getMessageBag()->toArray()['email'][0];
                $alertType = 'alert-danger';  
            } else {
                $userVerified = new EmailVerifiedService();
                $results = $userVerified->verifiedEmail($inputs['email']);
                if($results['returns']) {
                    $message = "You have successfully submitted your request. Please check your email.";
                    $alertType = "alert-success";
                } else {
                    if($results['is_active']) {
                        $message = "Your account is inactive. Please contact your Case Manager.";
                        $alertType = "alert-danger";
                    } else {
                        $message = "This email is not stored in our system. Try another email or contact your Case Manager";
                        $alertType = "alert-danger";
                    }
                }
            }
        }
        return View('site.home.forgot-password', [
            'message'=>$message,
            'alertType' => $alertType
        ]);
    }

    public function resetPassword($token) {
        $inputs = Input::all();
        $message = '';
        $alertType = '';
        $status = false;
        $roleID = 0;
        if (!empty($inputs)) {
            $rules = array(
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required|min:6'
            );
            $validator = Validator::make($inputs, $rules);
            if ($validator->fails()) {
                $message = $validator->getMessageBag()->toArray()['password'][0];
                $alertType = 'alert-danger';  
            } else {
                if(
                    preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*()_+])[A-Za-z\d][A-Za-z\d!@#$%^&*()_+]{5,20}$/i',$inputs['password'])
                ) {
                    $token = base64_decode($token.'=');
                    $data = explode('-',$token);
                    if(date('U') <= $data[0]){
                        $inputs['email']= $data[1];
                        $userVerified = new EmailVerifiedService();
                        $results = $userVerified->resetPassword($inputs);
                        if($results['returns']) {
                            $message = "You have successfully change your password.";
                            $alertType = 'alert-success';
                            $status = true;
                            $roleID = $results['role_id'];
                        } else {
                            $message = "Error, Failed to change your password";
                            $alertType = 'alert-danger';
                        }
                    } else {
                        $message = "Your token was expired.";
                        $alertType = 'alert-danger';
                    }
                } else {
                   $message = 'Password must contain at least one special character and one number';
                   $alertType = 'alert-danger';  
                }
            }
        }
        return View('site.home.reset-password', ['token'=>$token, 'message'=>$message, 'alertType' => $alertType, 'status' => $status, 'roleID'=>$roleID]);
    }

    public function logout() {
        $userID = Auth::id();
        Auth::logout();
        return View('site.home.login');
    }
}
