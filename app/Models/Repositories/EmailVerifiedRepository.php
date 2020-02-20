<?php

namespace App\Models\Repositories;

use App\Models\Entities\User;
use App\Models\Entities\PasswordResets;
use App\Models\Entities\RoleUser;
use Hash;

class EmailVerifiedRepository {
	private $user;
	private $passwordResets;
	private $roleUser;

	function __construct(){
		$this->user = new User();
		$this->passwordResets = new PasswordResets();
		$this->roleUser = new RoleUser();
	}

	function savePasswordResets($data) {
		$this->passwordResets->email = $data['email'];
		$this->passwordResets->token  $data['token'];
		$this->passwordResets->created_at = date('Y-m-d H:i:s');
		$save = $this->passwordResets->save();
		return $save;
	}
	
	function verifiedEmail($email) {
    	$results = $this->user->where('email','=',$email)->first();
    	return $results;
    }

    function changePassword($data) {
    	$results = $this->user->where('email','=',$data['email'])->where('is_active','=','1')->first();
    	$arr_return = array();
    	$arr_return['returns'] = false;
    	if(!empty($results)) {
    		$role = $this->roleUser->where('user_id','=',$results->id)->first();
    		$results->password = Hash::make($data['password']);
    		$results->update();
    		$arr_return['returns'] = true;
    		$arr_return['role_id'] = $role->role_id;
    	}
    	return $arr_return;
    }
}
