<?php

namespace App\Models\Repositories;

use App\Models\Entities\User;
use Illuminate\Support\Facades\Hash;

class UserRepository {
	private $user;

	function __construct(User $user){
		$this->user = $user;
	}

	function verifyActivationToken($token){
		$user = $this->user->where('password',$token)->where('is_active',0)->first();
		return $user;
	}

	function activateAccount($data){
		$user = User::find($data["user_id"]);
		$user->password = Hash::make($data["password"]);
		$user->is_active = 1;
		$user->update();
		return true;
	}
	
	function getAll(){
		$users = User::get();
		return $users;
	}

	function getUserDetails($id){
		$users = User::join('role_user', 'users.id','=','role_user.user_id')
					  ->where('users.id','=',$id)->first();
		return $users;
	}

	function isActiveUser($activeLinkLabel, $userID){
		if($activeLinkLabel=="1")
			$blnActive = 0;
		else
			$blnActive = 1;
		$user = User::find($userID);
		$user->is_active = $blnActive;
		$user->update();
		return $blnActive;
	}
}
