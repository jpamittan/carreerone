<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\Entities\Role;
use App\Models\Entities\Permission;
use App\Models\Entities\User;
use Illuminate\Support\Facades\Input;
use App\Models\Repositories\AdminRepository;
use Cache, Config, Cookie, Redirect, Request, Response, Session, URL, Validator, View;

class AuthController extends Controller {
  protected $validation = [
    'create' => [
      'email' => 'required|email',
      'password' => 'sometimes|required'
    ],
    'login' => [
      'email' => 'required|email',
      'password' => 'required|required'
    ]
  ];

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(User $user) {
    $this->user = $user;
  }

  public function index() {
    return View('admin.home.login');   
  }

  public function login() {
    $input = Input::all();
    $validator = Validator::make($input, $this->validation['login']);
    if ($validator->fails()) {
      return Response::json(
        ['success' => false, 'errors' => $validator->getMessageBag()->toArray()], 
        401
      );
    }
    $credentials = Input::only('email', 'password');
    if (!Auth::attempt($credentials)) {
      return Redirect::back()->withMessage('Invalid credentials');
    }
    $email = $input['email'];
    $password = $input['password'];
    $userID = Auth::id();
    $role = new AdminRepository();
    $roleID = $role->getRoleId($userID);
    $role_id = $roleID->role_id;
    if (Auth::attempt(array('email' => $email, 'password' => $password))) {
      if ($role_id == 1 || $role_id == 2) {
        return Redirect::to('admin/home');
      } else {
        return Redirect::back()->withMessage('You Dont Have Permission to Login, Please Check with Case Manager');
      } 
    }
  }
   
  public function create() {
    $input = Input::all();
    if ($input['event_value'] != 'edit') {
      $validator = Validator::make($input, $this->validation['create']);
      if ($validator->fails()) {
        return Response::json(
          ['success' => false, 'errors' => $validator->getMessageBag()->toArray()],
          422
        );
      }
    }
    $user = new AdminRepository();
    if ($input['event_value'] == 'new') {
      $results = $user->createUser($input);
    } else {
      $results = $user->updateUser($input);
    }
    if ($results) {
      return Response::json(
        ['success' => true]
      );
    } else {
      return Response::json(
        ['success' => false, 'errors' => 'email already exists']
      );
    }
  }

  public function getPermissions() {
    $permissions = new AdminRepository();
    $getpermissions = $permissions->getPermission();
    return Response::json(
      ['success' => true, 'permissions' => $getpermissions]
    );
  }

  public function postPermissions() {
    $inputs = Input::all();
    $permissions = new AdminRepository();
    $postpermissions = $permissions->postPermission($inputs);
    return Response::json(['success' => true]);
  }

  public function logout() {
    Auth::logout();
    return View('admin.home.login');   
  }
}
