<?php

namespace App\Models\Repositories;

use App\Models\Repositories\RepositoryBase;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\Entities\Role;
use App\Models\Entities\Permission;
use App\Models\Entities\User;
use View;
use URL;
use DB;

class AdminRepository extends RepositoryBase {
	public function createUser($input) {
		$email = $input['email'];
		$getEmail =  $this->checkemail($email);
		$email_id ='';
		if (isset($getEmail)) {
			$email_id = $getEmail->email;
		}
		if ($email_id == $email)	{
			return false;
		} else {
			$password = \Hash::make($input['password']);
			$user = new User();
			$user->first_name = $input['first_name'];
			$user->last_name = $input['last_name'];
			$user->email = $input['email'];
			$user->password = $password;
			$user->is_active = 1;
			$user->save();
			$user_id = $user->id;
			$role_id = $input['role'];
			DB::table('role_user')->insert(
			    ['user_id' => $user_id, 'role_id' =>$role_id]
			);
			return true;
		}
	}

	public function updateUser($input) {
		$user = new User();
		$rs = $user->where('id','=',$input['id'])->first();
		$rs->first_name = $input['first_name'];
		$rs->last_name = $input['last_name'];
		$rs->is_active = 1;
		$rs->update();
		$role_id = $input['role'];
		DB::table('role_user')->where('user_id','=',$rs->id)->delete();
		DB::table('role_user')->insert(
		    ['user_id' => $rs->id, 'role_id' =>$role_id]
		);
		if ($rs->type=='EiT') {
			DB::table('ins_employees')->where('user_id','=',$rs->id)->update(['new_personalemail'=>$rs->email]);
		}
		return true;
	}

	public function checkemail($email) {
		return DB::table('users')->where('email','=',$email)
				->select('email')
              	->first();
	}

	public function getPermission() {
		return DB::table('permissions')->select(array('name','id'))
				->orderBy('id','desc')
				->get();
	}
	public function postPermission($inputs) {
		DB::table('permission_role')->delete();
		foreach($inputs['role_check'] as $id => $input) {
			$perm_id = $id;
			foreach($input as $in) {
				$role_id = $in;
				DB::table('permission_role')->insert(
					[
						['permission_id' => $perm_id, 'role_id' => $role_id]
					]
				);
			}
		}
		return true;
	}

    public function getRoleId($userID) {
        return DB::table('role_user')->select(array('role_id'))
            ->where('user_id', '=', $userID)
            ->first();
    }

    /**
     * @param $userId
     * @return array|static
     */
    public function getRoles($userId) {
        $roles = \DB::table('role_user')->where(['user_id' => $userId])->get();
        if (!empty($roles)) {
            return collect($roles)->pluck('role_id')->toArray();
        }
        return [];
    }
}
