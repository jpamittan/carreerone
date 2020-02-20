<?php

namespace App\Models\Repositories;

use App\Models\Repositories\RepositoryBase;
use App\Models\Entities\SkillMatch;
use App\Models\Entities\User;
use App\Models\Factories\ExternalFileFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use DB, Config, Redirect, Session, URL, Validator, View;

class CaseManagerMatchRepository extends RepositoryBase {
	public function getjobDetails($job_id) {
		$jobs = DB::table('ins_jobs')
	 	->leftJoin('ins_agency_details', function($join) {
		    $userID = Auth::id();
		    $join->on('ins_agency_details.id', '=', 'ins_jobs.agency_id')
		   ;
		})
	   	->leftJoin('ins_job_category', function($join) {
		    $userID = Auth::id();
		    $join->on('ins_job_category.id', '=', 'ins_jobs.job_category_id');
		})
		->where('ins_jobs.jobid' ,'=', $job_id)
		->where('ins_agency_details.is_active' ,'1')
		->where('ins_job_category.is_active' ,'1')
		->select('ins_jobs.*','ins_agency_details.agency_name','ins_job_category.category_name')->distinct()->first();
		return $jobs;
	}

	public function getuserDetails($user_id) {
		return DB::table('users')
			->where('crm_user_id', '=', $user_id)
			->select(['users.*'])
			->first();
	}

	public function getEmployeeDetail($user_id) {
		return DB::table('ins_employees')
			->where('user_id', '=', $user_id)
			->select(['ins_employees.*'])
			->first();
	}

	public function getClientsDetail($user_id) {
		return DB::table('ins_clients')
			->where('user_id', '=', $user_id)
			->select(['ins_clients.*'])
			->first();
	}

	public function getuserDetailsinfo($user_id) {
		return DB::table('users')
			->where('id', '=',$user_id)
			->select(['users.*'])
			->first();
	}

	public function getCMDetailsinfo($id) {
   		return DB::table('ins_system_users')
	   		->where('systemuserid', '=',$id)
	   		->select(['ins_system_users.*'])
	   		->first();
    }

	public function getJobDetailsinfo($job_id) {
		return DB::table('ins_jobs')
			->leftJoin('ins_agency_details', function($join) {
				$userID = Auth::id();
				$join->on('ins_agency_details.id', '=', 'ins_jobs.agency_id');
			})
			->leftJoin('ins_job_category', function($join) {
				$userID = Auth::id();
				$join->on('ins_job_category.id', '=', 'ins_jobs.job_category_id');
			})
			->where('ins_jobs.id' ,'=', $job_id)
			->where('ins_agency_details.is_active' ,'1')
			->select('ins_jobs.*','ins_agency_details.agency_name','ins_job_category.category_name')
			->distinct()
				->first();
	}

	public function getJobMatch($job_id,$user_id) {
    	return DB::table('ins_jobmatch')
	    	->where('job_id', '=',$job_id)
	    	->where('candidate_id', '=',$user_id)
	    	->where('match_status', '=',121660000)
	    	->select(['ins_jobmatch.*'])
	    	->first();
    }

    public function getCapabilities($job_id,$user_id) {
		return DB::table('ins_capability')->leftJoin('ins_capability_match_names', function($join) {
			$join->on('ins_capability_match_names.id', '=', 'ins_capability.capability_name_id');
			})->leftJoin('ins_capability_group', function($join) {
				$join->on('ins_capability_match_names.group_id', '=', 'ins_capability_group.id');
			})->leftJoin('ins_capability_level', function($join) {
				$join->on('ins_capability_level.level_id', '=', 'ins_capability.level_id');
			})
			->where('candidate_id', '=',$user_id)
			->where('ins_capability.job_id', '=', $job_id)
			->select(['ins_capability_match_names.id',
			'ins_capability_match_names.match_names',
			'ins_capability_level.level_name',
			'ins_capability_group.group_name',
			'ins_capability_group.group_images',
			'ins_capability.score',
			'ins_capability.core_status'])
			->groupBy('ins_capability_match_names.match_names')
			->get();
	}

	public function getCapabilityScore($job_id,$user_id) {
		return DB::table('ins_capability')->where('candidate_id', '=',$user_id)->where('ins_capability.job_id', '=',$job_id)->sum('score');
	}

    public function getJobResumeID($job_id,$user_id) {
		$resume_id = DB::table('ins_cv')->where('job_id' ,'=', $job_id)
		->where('candidate_id' ,'=', $user_id)->select('id')
		->first();
		return $resume_id; 
	}

	public function getSkillMatch($job_id,$resume_id,$user_id) {
	   $skill_match = DB::table('ins_skillmatch')->leftJoin('ins_skillmatch_names', function($join) {
		    $join->on('ins_skillmatch_names.id', '=', 'ins_skillmatch.skill_id');
		})
		->where('job_id' ,'=', $job_id)
		->where('candidate_id' ,'=', $user_id)
		->where('resume_id' ,'=', $resume_id)
		->select(['ins_skillmatch_names.skill_name',
		'ins_skillmatch.status'])
		->get();
		return $skill_match;
	}

	public function getCandResumeID($user_id) {
		$userID = Auth::id();
		$resume_id =   DB::table('ins_cv')->where('candidate_id' ,'=', $user_id)
		->where('is_latest', '=', 1)
		->where('is_applied_resume', '=', 0)
		->select('id')
		->first();
		return $resume_id;
	}

	public function geteducation($user_id) {
       return DB::table('ins_user_education_info')
	   ->where('candidate_id' ,'=', $user_id)
	   ->select(['ins_user_education_info.*'])
	   ->get();
    }

    public function getWorkHistory($user_id) {
		return DB::table('ins_user_workhistory_info')
			->where('candidate_id' ,'=', $user_id)
			->select(['ins_user_workhistory_info.*'])
			->get();
    }

    public function getSkills($user_id) {
      return $skill_match = DB::table('ins_skill_candidate')
      		->leftJoin('ins_skillmatch_names', function($join) {
			    $join->on('ins_skillmatch_names.id', '=', 'ins_skill_candidate.skill_id');
			})
			->where('candidate_id' ,'=', $user_id)
			->select(['ins_skillmatch_names.skill_name'])
			->get();
    }

    public function getskillByJobID($job_id) {
   		return DB::table('ins_skillmatch_job')->where('job_id','=',$job_id)
    		->where('status','=',1)->select('ins_skillmatch_job.skill_id')->get();
	}

	public function getskillByCandidate($userID) {
		return  DB::table('ins_skill_candidate')->where('candidate_id','=',$userID)->select('ins_skill_candidate.skill_id')->get();
	}

	public function insertSkillMatch($match_status,$job_id,$userID) {
	    $skill_match = new SkillMatch();
	    foreach($match_status as $key=>$value) {
			$skill_match->job_id = $job_id;
			$skill_match->candidate_id = $userID;
			$skill_match->skill_id = $key;
			$skill_match->status = $value;
			$skill_match->save();
	    }
	}

	public function getSkillMatchCandidate($job_id,$userID) {
	    return DB::table('ins_skillmatch')
	            ->leftJoin('ins_skillmatch_names', function($join) {
	              $userID = Auth::id();
	              $join->on('ins_skillmatch_names.id', '=', 'ins_skillmatch.skill_id');
		        })
	            ->where('job_id','=',$job_id)->where('candidate_id','=',$userID)
	            ->select('ins_skillmatch.*','ins_skillmatch_names.skill_name')->get();
	}
}
