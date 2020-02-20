<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Services\UserService;

class AdminController extends Controller {
	private $userService;
	
	function __construct(UserService $userService) {
		$this->userService = $userService;
	}
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function home() {
        return View('admin.home.index');
    }

    public function userDetails() {
    	$users = $this->userService->getAllUsers();
        return View('admin.partials.users.index',['users'=>$users]);
    }

    public function userForm($id) {
        $users = array();
        $users['details'] = array();
        if (!empty($id)) {
            $users['details'] = $this->userService->getUserDetails($id);
        }
        return View('admin.partials.users.userform', $users);
    }

    public function permissionDetails() {
        return View('admin.partials.permissions.index');
    }

    public function permissionForm() {
        return View('admin.partials.permissions.permissionsform');
    }

    public function isActive() {
        $activeLinkLabel = Input::get('activeLinkLabel');
        $userID = Input::get('userID');
        $blnActive = $this->userService->isActive($activeLinkLabel, $userID);
        return $blnActive;
    }
}
