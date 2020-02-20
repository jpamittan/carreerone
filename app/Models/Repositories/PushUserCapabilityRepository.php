<?php

namespace App\Models\Repositories;

use App\Models\Entities\JobCandidateID;
use Illuminate\Support\Facades\Auth;
use DB;

class PushUserCapabilityRepository extends RepositoryBase {

	public function getUserCapabilityIDs(){
		return DB::table('ins_capability_candidate')->select("candidate_id")
		->groupBy("candidate_id")->get();
	}

	public function getUserID($user_id){
		return DB::table('ins_employees')->where('user_id','=',$user_id)->select(['employeeid'])->first();
	}
	
	public function getUserCapability($userID = null){
		$userID = ($userID) ? $userID : Auth::id();
		$jobApplied = DB::table('ins_capability_candidate')->leftJoin('ins_capability_match_names', function($join) {
				$join->on('ins_capability_candidate.capability_name_id', '=', 'ins_capability_match_names.id');
		})
		->where("ins_capability_candidate.candidate_id",$userID)
		->select(['ins_capability_candidate.*','ins_capability_match_names.crm_user_names'])
		->get();
		return $jobApplied;
	}
}
