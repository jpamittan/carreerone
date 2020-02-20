<?php

namespace App\Models\Repositories;

use App\Models\Repositories\RepositoryBase;
use DB;
use App\Models\Entities\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Config, Redirect, Session, URL, Validator, View;
use App\Models\Factories\ExternalFileFactory;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use App\Models\Entities\CapabilityMatch;

class CapabilityMatchRepository extends RepositoryBase {
	public function getCandidates() {
		return DB::table('ins_capability_candidate')
			->join('users', 'users.id', '=', 'ins_capability_candidate.candidate_id')
			->where('is_active', '=', 1)
			->select(['candidate_id'])
			->groupBy('candidate_id')
			->get();
	}

	public function getJobIDs() {
		return DB::table('ins_capability_job')
			->join('ins_jobs', 'ins_jobs.id', '=', 'ins_capability_job.job_id')
			->whereNull('is_expired')
			->select(['job_id'])
			->groupBy('job_id')
			->get();
	}

	public function getCandidateCapabilities($candidate_id) {
		return DB::table('ins_capability_candidate')->where('candidate_id','=',$candidate_id->candidate_id)->select(['capability_name_id','level_id', 'criteria'])->orderBy('capability_name_id','asc')->get();
	}

	public function getCandidateCapabilitiesCriteria($candidate_id) {
		$temp = array();
		$arr = array();
		$ins_capability_group =  DB::table('ins_capability_group')->orderBy('order_by', 'asc')->get();
		$counter = 0;
		foreach($ins_capability_group as $group) {
			$temp[$counter]['group']['name']  = $group->group_name;
			$temp[$counter]['group']['image']  = $group->group_images;
			$ins_capability_match_names = DB::select('select match_names as name, level_criteria_name   as criteria , level_id, capability_name_id, (select level_name from ins_capability_level where level_id = ins_capability_candidate.level_id)  as capability , core from `ins_capability_match_names` inner join `ins_capability_candidate` on `ins_capability_match_names`.`id` = `ins_capability_candidate`.`capability_name_id` inner join `ins_capability_level_criteria` on `ins_capability_level_criteria`.`level_criteria_id` = `ins_capability_candidate`.`criteria` where `group_id` = '.$group->id.' and `candidate_id` = '.$candidate_id->candidate_id.' order by `capability_name_id` asc') ;
			foreach($ins_capability_match_names as $names) {
				$temp[$counter]['capabilities'][] = $names;
			}
			if(empty($temp[$counter]['capabilities'])) {
				unset($temp[$counter]);
			}
			$counter++;
		}
		return $temp;
	}

	public function getJobCapabilities($job_id) {
		return DB::table('ins_capability_job')
			->join('ins_capability_level', 'ins_capability_level.level_id', '=', 'ins_capability_job.level_id')
			->where('job_id','=',$job_id->job_id)
			->select('ins_capability_job.*', 'ins_capability_level.level_name','ins_capability_job.core_status')
			->orderBy('capability_name_id','asc')
			->get();
	}

	public function chkCapabMatch($capabilities, $job_id, $candidate_id) {
		return DB::table('ins_capability')->where('job_id','=',$job_id->job_id)
										->where('capability_name_id','=',$capabilities)
										->where('candidate_id','=',$candidate_id->candidate_id)
										->select('score')
										->first();
	}

	public function insertCapabMatch($capabilities, $level, $score, $job_id, $candidate_id, $core_status) {
		$capability_match = new CapabilityMatch();
		$capability_match->job_id = $job_id->job_id;
		$capability_match->candidate_id = $candidate_id->candidate_id;
		$capability_match->capability_name_id = $capabilities;
		$capability_match->level_id = $level;
		$capability_match->core_status = $core_status;
		$capability_match->score = $score;
		$capability_match->save();
	}

	public function UpdateCapabpercentage($id,$percent) {
		return DB::table('ins_capability')->where('id','=',$id)
										->update(['percentage' => $percent]); 
	}

	public function getCpabaMatchID($capability,$job_id,$candidate_id) {
		return DB::table('ins_capability')->where('job_id','=',$job_id->job_id)
										->where('capability_name_id','=',$capability)
										->where('candidate_id','=',$candidate_id->candidate_id)
										->select(['ins_capability.*'])->first(); 
	}

	public function getJobCoreStatus($job_id,$capability_id,$level_id) {
		return DB::table('ins_capability_job')->where('job_id','=',$job_id->job_id)
							->where('capability_name_id','=',$capability_id)
							->where('level_id','=',$level_id)->select(['ins_capability_job.core_status'])->first();
	}

	public function updateCapabMatch($cab_match_id,$level_id,$score,$candidate_id) {
		return DB::table('ins_capability')->where('id','=',$cab_match_id->id)
							->where('candidate_id','=',$candidate_id->candidate_id)
										->update(['score' => $score,
											'level_id' => $level_id]); 
	}

	public function getCore1($job_id) {
		return DB::table('ins_capability_job')->where('job_id','=',$job_id->job_id)
							->where('core_status','=',1)
							->select(['ins_capability_job.core_status','ins_capability_job.core_status'])->count();
	}

	public function getCore0($job_id) {
		return DB::table('ins_capability_job')->where('job_id','=',$job_id->job_id)
							->where('core_status','=',0)
							->select(['ins_capability_job.core_status','ins_capability_job.core_status'])->count();
	}
}
