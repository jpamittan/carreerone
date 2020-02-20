<?php

namespace App\Models\Repositories;

use App\Models\Repositories\RepositoryBase;
use App\Models\Containers\ResumeExtract;
use App\Models\Gateways\RedactGateway;
use App\Models\Proxies\FileProxy;
use App\Models\Gateways\Redact\ResumeRedact;
use App\Models\Entities\User;
use App\Models\Entities\SkillMatchNames;
use App\Models\Entities\CandidateSkillMatch;
use App\Models\Entities\SkillMatch;
use App\Models\Entities\JobSkillMatch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Config, Redirect, Session, URL, Validator, View;
use App\Models\Factories\ExternalFileFactory;
use App\Models\Entities\Resumes;
use App\Models\Entities\JobMatchCandidate;
use App\Models\Entities\CategoryNames;
use App\Models\Entities\JobMatchCandidateCategory;
use App\Models\Entities\JobMatch;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use App\Models\Entities\InterviewConfirmation;
use App\Models\Entities\RssFeeds;
use App\Models\Entities\JobReject;

class JobRepository extends RepositoryBase {
  public function getResumeID($candidate_id) {
    $resume = DB::table('ins_cv')->where('candidate_id', '=', $candidate_id)
    ->where('is_latest', '=', 1)
    ->where('is_applied_resume', '=', 0)
    ->select(['id'])
    ->first();
    $resume_id = $resume->id;
    return  $resume_id;
  }

  public function getCandidateSlaryFrom($job_id, $candidate_id) {
    $salary_from_candidate = DB::table('ins_jobmatch_candidate')
    ->where('candidate_id', '=', $candidate_id)
    ->where('job_id', '=', $job_id)
    ->select(['ins_jobmatch_candidate.*'])
    ->first();
    return $salary_from_candidate;
  }

  public function getJobSlaryFrom($job_id) {
    $salary_from_job = DB::table('ins_jobs')->where('id', '=', $job_id)
    ->select(['salary_from','salary_to','suburb','state','postcode','job_category_id'])
    ->first();
    return $salary_from_job;
  }

  public function getJobID() {
    $job_ids = DB::table('ins_jobs')->select(['id','salary_package'])->orderBy('id','asc')->get();
    return $job_ids;
  }

  public function postJobMatchStatus1($job_id, $candidate_id) {
    DB::table('ins_jobmatch')->where('job_id', '=', $job_id)->where('candidate_id','=', $candidate_id)->delete();
    $job_match = new JobMatch();
    $job_match->job_id = $job_id;
    $job_match->candidate_id = $candidate_id;
    $job_match->match_status = 1;
    $job_match->save();
  }

  public function postJobMatchStatus0($job_id, $candidate_id) {
    DB::table('ins_jobmatch')->where('job_id', '=', $job_id)->where('candidate_id','=', $candidate_id)->delete();
    $job_match = new JobMatch();
    $job_match->job_id = $job_id;
    $job_match->candidate_id = $candidate_id;
    $job_match->match_status = 0;
    $job_match->save();
  }

  public function getCandidateCategory($candidate_id, $job_id) {
    $category_ids =   DB::table('ins_jobmatch_candidate_industry')
    ->where('candidate_id', '=', $candidate_id)
    ->where('job_id', '=', $job_id)
    ->select(['category_id'])->get();
    return $category_ids;
  }

  public function getAllJobs() {
    $jobs = DB::table('ins_jobs')->get();
    return $jobs;
  }

  public function getFutureJobs($params= array(), $page =1, $limit =20, $type='matched', $notinmatchids=array()) {
    $query = DB::table('ins_jobs')
    ->where(function ($query) {
      $query->where('ins_jobs.is_expired','=',null)
            ->OrWhere('ins_jobs.deadline_date','>=', date('Y-m-d'));
    })  
    ->orderBy('ins_jobs.id', 'asc');
    if(!empty($notinmatchids)) {
      $query = $query->whereNotIn('ins_jobs.id', $notinmatchids);
    }
    $jobs_count = $query->get(); 
    $count = count($jobs_count);
    $query->offset(($page-1) * $limit)->limit($limit);
    $jobs = $query->get(); 
    $jobs1['count'] = $count;
    $jobs1['result'] = $jobs;
    return $jobs1;
  }

  public function getMatchedJobs($params= array(), $page =1, $limit =20, $type='matched', $notinmatchids=array()) {
    $search_keywords ='';
    $search_location ='';
    $search_category ='';
    if(!empty( $params['search_keywords'])) {
      $search_keywords =trim($params['search_keywords']);
    }
    if(!empty( $params['search_location'])) {
      $search_location =trim($params['search_location']);
    }
    if(!empty( $params['search_category'])) {
      $search_category =trim($params['search_category']);
    }
    $application_monitor_match = Config::get('ins.application_monitor_match');
    $application_monitor_applied = Config::get('ins.application_monitor_applied');
    $query = DB::table('ins_jobs')
    ->leftJoin('ins_job_candidate', function($join)
    {
      $userID = Auth::id();
      $join->on('ins_jobs.id', '=', 'ins_job_candidate.job_id');
      $join->on('ins_job_candidate.candidate_id',  '=', DB::raw( $userID));
    })
    ->leftJoin('ins_agency_details', function($join)
    {
      $userID = Auth::id();
      $join->on('ins_agency_details.id', '=', 'ins_jobs.agency_id');
    })
    ->leftJoin('ins_job_category', function($join)
    {
      $userID = Auth::id();
      $join->on('ins_job_category.id', '=', 'ins_jobs.job_category_id');
    })
    ->leftJoin('ins_jobmatch', function($join)
    {
      $userID = Auth::id();
      $join->on('ins_jobs.id', '=', 'ins_jobmatch.job_id');
      $join->on('ins_jobmatch.candidate_id', '=', DB::raw( $userID));
    })
    ->whereIn('ins_jobmatch.match_status', $application_monitor_match)
    ->whereIn('ins_job_candidate.ins_progress', $application_monitor_applied)
    ->whereNull('ins_jobs.is_expired')
    ->where('ins_agency_details.is_active' ,'1')
    ->select('ins_jobs.*','ins_jobmatch.match_status as jobmatchstatus','ins_job_candidate.job_id as job_apply','ins_job_candidate.updated_at as job_apply_date'
      ,'ins_agency_details.agency_name','ins_job_category.category_name' ,'ins_job_candidate.submit_status')
    ->orderBy('ins_jobs.id', 'asc')
    ->groupBy('ins_jobs.id');
    if(!empty( $search_keywords)) {
      $query = $query->where('ins_jobs.job_title','like', '%'.$search_keywords.'%');
    }
    if(!empty( $search_location)) {
      $query = $query->where('ins_jobs.location','like', '%'.$search_location.'%');
    }
    $query_matched = null;
    if(!empty($notinmatchids)) {
      $query = $query->whereNotIn('ins_jobs.id', $notinmatchids);
    }
    $jobs_count = $query->get();
    $count = count($jobs_count);
    $query->offset(($page-1) * $limit)->limit($limit);
    $jobs = $query->get();
    $jobs1['count'] = $count;
    $jobs1['result'] = $jobs;
    return $jobs1;
  }

  public function getHistoryJobs($params = array(), $page = 1, $limit = 20, $type = 'ins', $notinmatchids = array()) {
    $search_keywords = '';
    $search_location = '';
    $search_category = '';
    if (!empty($params['search_keywords'])) {
        $search_keywords = trim($params['search_keywords']);
    }
    if (!empty($params['search_location'])) {
        $search_location = trim($params['search_location']);
    }
    if (!empty($params['search_category'])) {
        $search_category = trim($params['search_category']);
    }
    $match_history = Config::get('ins.match_history');
    $potential_match = Config::get('ins.potential_match');
    $all_matches = array_merge($potential_match, $match_history);
    $query = DB::table('ins_jobs')
        ->leftJoin('ins_job_candidate', function ($join) {
            $userID = Auth::id();
            $join->on('ins_jobs.id', '=', 'ins_job_candidate.job_id');
            $join->on('ins_job_candidate.candidate_id', '=', DB::raw($userID));
        })
        ->leftJoin('ins_agency_details', function ($join) {
            $userID = Auth::id();
            $join->on('ins_agency_details.id', '=', 'ins_jobs.agency_id');
        })
        ->leftJoin('ins_job_category', function ($join) {
            $userID = Auth::id();
            $join->on('ins_job_category.id', '=', 'ins_jobs.job_category_id');
        })
        ->leftJoin('ins_jobmatch', function ($join) {
            $userID = Auth::id();
            $join->on('ins_jobs.id', '=', 'ins_jobmatch.job_id');
            $join->on('ins_jobmatch.candidate_id', '=', DB::raw($userID));
        })
        ->whereIn('ins_jobmatch.match_status', $all_matches)
        ->where(function ($query) {

            $query->where('ins_jobs.is_expired', '=', null)
                ->OrWhere('ins_jobs.deadline_date', '<', date('Y-m-d'));
        })
        ->where('ins_agency_details.is_active', '1')
        ->select('ins_jobs.*', 'ins_jobmatch.match_status as jobmatchstatus', 'ins_job_candidate.job_id as job_apply', 'ins_job_candidate.updated_at as job_apply_date'
            , 'ins_agency_details.agency_name', 'ins_job_category.category_name')
        ->orderBy('ins_jobs.id', 'asc')
        ->groupBy('ins_jobs.id');
    if (!empty($search_keywords)) {
        $query = $query->where('ins_jobs.job_title', 'like', '%' . $search_keywords . '%');
    }
    if (!empty($search_location)) {
        $query = $query->where('ins_jobs.location', 'like', '%' . $search_location . '%');
    }
    if ($type == 'ins') {
        $query = $query->whereNotIn('ins_jobs.id', function ($q) {
          $userID = Auth::id();
          $q->select('job_id')->from('ins_job_candidate')->where('ins_job_candidate.candidate_id', '=', $userID)->where('ins_job_candidate.job_id', '!=', 'ins_jobs.id');
        });
    }
    if (!empty($notinmatchids)) {
      $query = $query->whereNotIn('ins_jobs.id', $notinmatchids);
    }
    $jobs = $query->get();
    $count = count($jobs);
    $query->offset(($page - 1) * $limit)->limit($limit);
    $jobs = $query->get();
    $jobs1['count'] = $count;
    $jobs1['result'] = $jobs;
    return $jobs1;
  }

  public function getJobDetails($params= array(), $page =1, $limit =20, $type='ins', $notinmatchids=array()) {
    $search_keywords ='';
    $search_location ='';
    $search_category ='';
    if(!empty( $params['search_keywords'])) {
      $search_keywords =trim($params['search_keywords']);
    }
    if(!empty( $params['search_location'])) {
      $search_location =trim($params['search_location']);
    }
    if(!empty( $params['search_category'])) {
      $search_category =trim($params['search_category']);
    }
    $potential_match = Config::get('ins.potential_match');
    $query = DB::table('ins_jobs')
    ->leftJoin('ins_agency_details', function($join) {
      $userID = Auth::id();
      $join->on('ins_agency_details.id', '=', 'ins_jobs.agency_id');
    })
    ->leftJoin('ins_job_category', function($join) {
      $userID = Auth::id();
      $join->on('ins_job_category.id', '=', 'ins_jobs.job_category_id');
    })
    ->leftJoin('ins_jobmatch', function($join) {
      $userID = Auth::id();
      $join->on('ins_jobs.id', '=', 'ins_jobmatch.job_id');
      $join->on('ins_jobmatch.candidate_id',  '=', DB::raw( $userID));
    })
    ->Where(function ($query) {
      $potential_match_3pm = Config::get('ins.potential_match_3pm');
      $query->whereIn('ins_jobmatch.match_status', $potential_match_3pm)
            ->where('ins_jobs.is_expired','=',null)
            ->where('ins_jobs.deadline_date','>=', date('Y-m-d 14:59:59'));
    }) 
    ->orWhere(function ($query) {
      $potential_match_1pm = Config::get('ins.potential_match_1pm');
      $query->whereIn('ins_jobmatch.match_status', $potential_match_1pm)
            ->where('ins_jobs.is_expired','=',null)
            ->where('ins_jobs.deadline_date','>=', date('Y-m-d 12:59:59'));
    }) 
    ->where('ins_agency_details.is_active','1')
    ->select('ins_jobs.*','ins_jobmatch.match_status as jobmatchstatus','ins_agency_details.agency_name','ins_job_category.category_name')
    ->orderBy('ins_jobs.id', 'asc')
    ->groupBy('ins_jobs.id');
    if(!empty( $search_keywords)) {
      $query = $query->where('ins_jobs.job_title','like', '%'.$search_keywords.'%');
    }
    if(!empty( $search_location)) {
      $query = $query->where('ins_jobs.location','like', '%'.$search_location.'%');
    }
    if(!empty($notinmatchids)) {
      $query = $query->whereNotIn('ins_jobs.id', $notinmatchids);
    }
    $jobs = $query->get(); 
    $count = count($jobs);
    $query->offset(($page-1) * $limit)->limit($limit);
    $jobs = $query->get(); 
    $jobs1['count'] = $count;
    $jobs1['result'] = $jobs;
    return $jobs1;
  }

  public function getjobdetail($params= array(), $page =1, $limit =20, $type='ins', $notinmatchids=array()) {
    $search_keywords ='';
    $search_location ='';
    $search_category ='';
    if(!empty( $params['search_keywords'])) {
       $search_keywords =trim($params['search_keywords']);
    }
     if(!empty( $params['search_location'])) {
       $search_location =trim($params['search_location']);
    }
     if(!empty( $params['search_category'])) {
       $search_category =trim($params['search_category']);
    }
    $query = DB::table('ins_jobs')
    ->leftJoin('ins_job_candidate', function($join) {
      $userID = Auth::id();
      $join->on('ins_jobs.id', '=', 'ins_job_candidate.job_id');
      $join->on('ins_job_candidate.candidate_id',  '=', DB::raw( $userID));
    })
    ->leftJoin('ins_user_rejected_jobs', function($join) {
      $userID = Auth::id();
      $join->on('ins_jobs.id', '!=', 'ins_user_rejected_jobs.job_id');
      $join->on('ins_user_rejected_jobs.candidate_id',  '=', DB::raw( $userID));
    })
    ->leftJoin('ins_agency_details', function($join) {
      $userID = Auth::id();
      $join->on('ins_agency_details.id', '=', 'ins_jobs.agency_id');
    })
    ->leftJoin('ins_job_category', function($join) {
      $userID = Auth::id();
      $join->on('ins_job_category.id', '=', 'ins_jobs.job_category_id');
    })
    ->leftJoin('ins_cv', function($join) {
      $userID = Auth::id();
      $join->on('ins_cv.job_id', '=', 'ins_jobs.id');
    })
    ->whereNotIn('ins_jobs.id', function($q) {
      $userID = Auth::id();
      $q->select('job_id')->from('ins_user_rejected_jobs')->where('ins_user_rejected_jobs.candidate_id', '=', $userID)->where('ins_user_rejected_jobs.job_id', '!=','ins_jobs.id');
    })
    ->where('ins_jobs.is_expired','=',null)
    ->where('ins_agency_details.is_active' ,'1')
    ->select('ins_jobs.*','ins_job_candidate.job_id as job_apply','ins_job_candidate.updated_at as job_apply_date'
    ,'ins_agency_details.agency_name','ins_job_category.category_name','ins_cv.status as cv_status','ins_cv.is_latest as latest','ins_job_candidate.submit_status')
    ->orderBy('ins_jobs.id', 'asc')
    ->groupBy('ins_jobs.id');
    if(!empty( $search_keywords)) {
      $query = $query->where('ins_jobs.job_title','like', '%'.$search_keywords.'%');
    }
    if(!empty( $search_location)) {
      $query = $query->where('ins_jobs.location','like', '%'.$search_location.'%');
    }
    if($type == 'ins') {
      $query = $query->whereNotIn('ins_jobs.id', function($q) {
      $userID = Auth::id();
      $q->select('job_id')->from('ins_job_candidate')->where('ins_job_candidate.candidate_id', '=', $userID)->where('ins_job_candidate.job_id', '!=','ins_jobs.id');
      });
    }
    if($type == 'matched') {
      $query_matched  =null;//
      $jobapplied = DB::table('ins_job_candidate')->select('job_id')->where('candidate_id' ,'=' ,  Auth::id())->get();
      $jobapplied_arr=array();
      $jobapplied_arr = json_decode(json_encode($jobapplied), true);
      $query = $query->orWhere('ins_job_candidate.candidate_id', '=',Auth::id())->
      orWhereIn('ins_job_candidate.job_id', $jobapplied_arr);
    }
    if(!empty($notinmatchids)) {
      $query = $query->whereNotIn('ins_jobs.id', $notinmatchids);
    }
    $jobs = $query->get(); 
    $count = count($jobs);
    $query->offset(($page-1) * $limit)->limit($limit);
    $jobs = $query->get(); 
    $jobs1['count'] = $count;
    $jobs1['result'] = $jobs;
    return $jobs1;
  }

  public function insertJobEoi($job_id  , $status , $ins_job_apply_id , $comments = null) {
    $userID = Auth::id();
    $id =  DB::table('ins_job_candidate_eoi')->insertGetId(
      [
        'job_id' => $job_id , 
        'candidate_id' => $userID , 
        'ins_pushed' => 'Y', 
        'submit_status' => $status, 
        'ins_job_apply_id' => $ins_job_apply_id, 
        'comments' => $comments,
        'created_at' => date('Y-m-d H:i:s'), 
        'updated_at' => date('Y-m-d H:i:s')
      ]
    );
    return $id;
 }
 
  public function getjob($job_id) {
    $jobs = DB::table('ins_jobs')
    ->leftJoin('ins_agency_details', function($join) {
      $userID = Auth::id();
      $join->on('ins_agency_details.id', '=', 'ins_jobs.agency_id');
    })
    ->leftJoin('ins_job_category', function($join) {
      $userID = Auth::id();
      $join->on('ins_job_category.id', '=', 'ins_jobs.job_category_id');
    })
    ->leftJoin('ins_job_candidate', function($join) {
      $userID = Auth::id();
      $join->on('ins_jobs.id', '!=', 'ins_job_candidate.job_id');
      $join->on('ins_job_candidate.candidate_id',  '=', DB::raw( $userID));
    })
    ->leftJoin('ins_role_desc_pdf', function($join) {
      $join->on('ins_role_desc_pdf.job_id', '=', 'ins_jobs.id')
      ->where('ins_role_desc_pdf.is_latest', '=', 1);
    })
    ->leftJoin('ins_jobmatch', function($join) {
      $userID = Auth::id();
      $join->on('ins_jobs.id', '=', 'ins_jobmatch.job_id');
      $join->on('ins_jobmatch.candidate_id',  '=', DB::raw( $userID));
    })
    ->where('ins_jobs.id' ,'=', $job_id)
    ->where('ins_agency_details.is_active' ,'1')
    ->select('ins_jobs.*','ins_jobmatch.match_status as jobmatchstatus', 'ins_agency_details.agency_name','ins_job_category.category_name','ins_job_candidate.submit_status','ins_role_desc_pdf.url as rd_url' ,'ins_role_desc_pdf.id as rd_id', 'ins_role_desc_pdf.name as rd_name')
    ->distinct()
    ->first();
    return $jobs;
  }

  public function rejectJob($job_id) {
    $userID = Auth::id();
    $job_reject = new JobReject();
    $job_reject->job_id = $job_id;
    $job_reject->candidate_id = $userID;
    $job_reject->save();
  }

  public function getJobstatus() {
    $userID = Auth::id();
    $job_ids =  DB::table('ins_job_candidate')->where('candidate_id', '=', $userID)
    ->select(['job_id'])
    ->groupBy('job_id')->orderBy('job_id','asc')->get();
    return $job_ids;
  }

  public function applyHistory() {
    $userID = Auth::id();
    $apply_history =  DB::table('ins_jobs')
    ->leftJoin('ins_job_candidate', function($join) {
      $userID = Auth::id();
      $join->on('ins_jobs.id', '=', 'ins_job_candidate.job_id');
      $join->on('ins_job_candidate.candidate_id',  '=', DB::raw( $userID));
    })
    ->leftJoin('ins_agency_branch', function($join) {
      $userID = Auth::id();
      $join->on('ins_agency_branch.id', '=', 'ins_jobs.agency_branch_id');
    })
    ->leftJoin('ins_agency_details', function($join) {
      $userID = Auth::id();
      $join->on('ins_agency_details.id', '=', 'ins_agency_branch.agency_id');
    })
    ->leftJoin('ins_job_category', function($join) {
      $userID = Auth::id();
      $join->on('ins_job_category.id', '=', 'ins_jobs.job_category_id');
    })
    ->leftJoin('ins_cv', function($join) {
      $userID = Auth::id();
      $join->on('ins_cv.job_id', '=', 'ins_jobs.id');
    })
    ->leftJoin('ins_user_rejected_jobs', function($join) {
      $userID = Auth::id();
      $join->on('ins_jobs.id', '!=', 'ins_user_rejected_jobs.job_id');
      $join->on('ins_user_rejected_jobs.candidate_id',  '=', DB::raw( $userID));
    })
    ->whereNotIn('ins_jobs.id', function($q) {
      $userID = Auth::id();
    $q->select('job_id')->from('ins_user_rejected_jobs')->where('ins_user_rejected_jobs.candidate_id', '=', $userID)->where('ins_user_rejected_jobs.job_id', '!=','ins_jobs.id');
    }) 
    ->where('ins_job_candidate.candidate_id' ,'=', $userID)
    ->where('ins_job_candidate.deleted_at' ,'=', NULL)
    ->select('ins_jobs.*','ins_agency_details.agency_name','ins_job_category.category_name','ins_job_candidate.updated_at as created_at',
      'ins_cv.id as cv_id','ins_cv.status as cv_status','ins_cv.resume_name','ins_cv.is_latest as latest','ins_job_candidate.submit_status')
    ->groupBy('ins_jobs.id')
    ->paginate(3);
    return $apply_history;
  }

  public function getResume() {
    $userID = Auth::id();
    return DB::table('ins_cv')->select(['ins_cv.*','jct.category_name as category_name'])->where('candidate_id', '=', $userID)
      ->leftJoin('ins_job_category as jct', function($join) {
        $join->on('jct.id', '=', 'ins_cv.category_id');
      })
      ->where('is_applied_resume', '=', 0)
      ->orderBy('id', 'desc')->limit(5)->where('status' ,'=',1)->get();
  }

  public function getSkillJobID() {
    $job_ids = DB::table('ins_skillmatch_job')
    ->select('job_id')
    ->groupBy('job_id')
    ->get();
    return $job_ids;
 }

  public function matchSkillJobID($job_id) {
    $match_job_id =  CandidateSkillMatch::where('job_id', '=', $job_id)->exists();
    if($match_job_id == null) {
      return 0;
    } else {
      return 1;
    }
  }

  public function getSkillID($job_id) {
    $skill_ids = DB::table('ins_skillmatch_job')->where('job_id', '=', $job_id)
    ->select('skill_id')
    ->get();
    return $skill_ids;
  }

  public function getCandidateID($job_id) {
    $candidate_ids = DB::table('ins_cv')->where('job_id', '=', $job_id)->select('candidate_id')
    ->groupBy('candidate_id')->get();
    return $candidate_ids;
  }

  public function getSkillResumeID($candidate_id, $job_id) {
    $resume_ids =   DB::table('ins_cv')->where('job_id' ,'=', $job_id)
    ->where('candidate_id' ,'=', $candidate_id)->select('id')
    ->get()
    return $resume_ids;
  }

  public function skill_match_candidate($job_id, $candidate_id, $resume_id, $skill_id) {
    $skill_match = CandidateSkillMatch::where('candidate_id', '=', $candidate_id)
    ->where('resume_id', '=', $resume_id)
    ->where('skill_id', '=', $skill_id)
    ->exists();
    if($skill_match == null) {
      return '0';
    } else {
      return '1';
    }  
  }

  public function deleteSkillMach() {
    DB::table('ins_skillmatch')->delete(); 
  }

  public function postSkillMatch($job_id, $candidate_id, $resume_id, $skill_id, $status) {
    $skill_match = new SkillMatch();
    $skill_match->job_id = $job_id;
    $skill_match->candidate_id = $candidate_id;
    $skill_match->skill_id = $skill_id;
    $skill_match->status = $status;
    $skill_match->save();
  }

  public function getJobResumeID($job_id) {
    $userID = Auth::id();
    $resume_id =   DB::table('ins_cv')->where('job_id' ,'=', $job_id)
    ->where('candidate_id' ,'=', $userID)->select('id')
    ->first();
    return $resume_id;
  }

  public function getSkillMatch($job_id, $resume_id) {
    $userID = Auth::id();
    $skill_match = DB::table('ins_skillmatch')->leftJoin('ins_skillmatch_names', function($join) {
      $userID = Auth::id();
      $join->on('ins_skillmatch_names.id', '=', 'ins_skillmatch.skill_id');
    })
    ->where('job_id' ,'=', $job_id)
    ->where('candidate_id' ,'=', $userID)
    ->where('resume_id' ,'=', $resume_id)
    ->select(['ins_skillmatch_names.skill_name',
    'ins_skillmatch.status'])
    ->get();
    return $skill_match;
  }

  public function insertJobSkill($jobId, $skill) {     
    $skill_match = DB::table('ins_skillmatch_names')
    ->where('skill_name', '=', $skill)
    ->first();
    if (isset($skill_match)) {
      $skill_match_name = $skill_match->skill_name;
      $skill_count = $skill_match->count;
      $skill_id = $skill_match->id;
      if ($skill == $skill_match_name) {
        DB::table('ins_skillmatch_names')
          ->where('skill_name', '=', $skill_match_name)
          ->update(['count' => ++$skill_count]);
      }
    } else {
      $skill_match_name = new SkillMatchNames();
      $skill_match_name->skill_name =  $skill;
      $skill_match_name->count =  0;
      $skill_match_name->save();
      $skill_id = $skill_match_name->id;
    }
    $skill_match = DB::table('ins_skillmatch_job')
        ->where('job_id', '=', $jobId)
        ->where('skill_id', '=', $skill_id)
        ->first();
    if ($skill_match) {
      return;
    }
    $jobSkillMatch = new JobSkillMatch();
    $jobSkillMatch->job_id = $jobId;
    $jobSkillMatch->skill_id = $skill_id;
    $jobSkillMatch->status = 1;
    $jobSkillMatch->save();
  }

  public function getUserCandidateID() {
    $user_cand_id =   DB::table('role_user')->where('role_id' ,'=', 3)
    ->select(['user_id'])
    ->get();
    return $user_cand_id;
  }

  public function getUserResumeID($candidate_id) {
    $resume_id =  DB::table('ins_cv')->where('candidate_id', '=', $candidate_id)
    ->where('is_latest', '=', 1)
    ->where('is_applied_resume', '=', 0)
    ->select(['id'])
    ->first();
    return  $resume_id;
  }

  public function getCandResumeID() {
    $userID = Auth::id();
    $resume_id =   DB::table('ins_cv')->where('candidate_id' ,'=', $userID)
    ->where('is_latest', '=', 1)
    ->where('is_applied_resume', '=', 0)
    ->select('id')
    ->first();
    return $resume_id;
  }

  public function getJobsone() {
    $jobs = DB::table('ins_jobs')
    ->leftJoin('ins_job_candidate', function($join) {
      $userID = Auth::id();
      $join->on('ins_jobs.id', '=', 'ins_job_candidate.job_id');
      $join->on('ins_job_candidate.candidate_id',  '=', DB::raw( $userID));
    })
    ->select('ins_jobs.*','ins_job_candidate.job_id as job_apply')
    ->where('ins_jobs.is_expired', '=', null)
    ->limit(3)
    ->groupBy('ins_jobs.id')
    ->orderBy('appreoved_date', 'desc')
    ->paginate(3);
    return $jobs;
  }

  public function getJobstwo() {
    $jobs = DB::table('ins_jobs')
    ->leftJoin('ins_job_candidate', function($join) {
      $userID = Auth::id();
      $join->on('ins_jobs.id', '=', 'ins_job_candidate.job_id');
      $join->on('ins_job_candidate.candidate_id', '=', DB::raw($userID));
    })
    ->select('ins_jobs.*','ins_job_candidate.job_id as job_apply')
    ->where('ins_jobs.is_expired', '=', null)
    ->limit(4)
    ->groupBy('ins_jobs.id')
    ->orderBy('appreoved_date', 'desc')
    ->paginate(4);
    return $jobs;
  }

  public function getJobsPosted() {
    $jobs = DB::table('ins_jobs')
    ->leftJoin('ins_job_candidate', function($join) {
      $userID = Auth::id();
      $join->on('ins_jobs.id', '=', 'ins_job_candidate.job_id');
      $join->on('ins_job_candidate.candidate_id',  '=', DB::raw( $userID));
    })
    ->leftJoin('ins_agency_details', function($join) {
      $userID = Auth::id();
      $join->on('ins_agency_details.id', '=', 'ins_jobs.agency_id');
    })
    ->leftJoin('ins_job_category', function($join) {
      $userID = Auth::id();
      $join->on('ins_job_category.id', '=', 'ins_jobs.job_category_id');
    })
    ->leftJoin('ins_cv', function($join) {
      $userID = Auth::id();
      $join->on('ins_cv.job_id', '=', 'ins_jobs.id');
    })
    ->where('ins_jobs.is_expired','=',null)
    ->select('ins_jobs.*','ins_job_candidate.job_id as job_apply','ins_job_candidate.updated_at as job_apply_date'
    ,'ins_agency_details.agency_name','ins_job_category.category_name','ins_cv.status as cv_status','ins_cv.is_latest as latest')
    ->where('ins_jobs.is_expired', '=', null)
    ->where('ins_agency_details.is_active' ,'1')
    ->orderBy('appreoved_date', 'desc')
    ->groupBy('ins_jobs.id')->paginate(10);
    return $jobs;
  }

  public function getJobsExpiring() {
    $jobs = DB::table('ins_jobs')
    ->leftJoin('ins_job_candidate', function($join) {
      $userID = Auth::id();
      $join->on('ins_jobs.id', '=', 'ins_job_candidate.job_id');
      $join->on('ins_job_candidate.candidate_id',  '=', DB::raw( $userID));
    })
    ->leftJoin('ins_agency_details', function($join) {
      $userID = Auth::id();
      $join->on('ins_agency_details.id', '=', 'ins_jobs.agency_id');
    })
    ->leftJoin('ins_job_category', function($join) {
      $userID = Auth::id();
      $join->on('ins_job_category.id', '=', 'ins_jobs.job_category_id');
    })
    ->leftJoin('ins_cv', function($join) {
      $userID = Auth::id();
      $join->on('ins_cv.job_id', '=', 'ins_jobs.id');
    })
    ->where('ins_jobs.is_expired','=',null)
    ->select('ins_jobs.*','ins_job_candidate.job_id as job_apply','ins_job_candidate.updated_at as job_apply_date'
    ,'ins_agency_details.agency_name','ins_job_category.category_name','ins_cv.status as cv_status','ins_cv.is_latest as latest')
    ->where('ins_jobs.is_expired', '=', null)
    ->where('ins_agency_details.is_active' ,'1')
    ->orderBy('appreoved_date', 'asc')
    ->groupBy('ins_jobs.id')->paginate(10);
    return $jobs;
  }

  public function getCandidateInterviews() {
    $userID = Auth::id();
    $get_candidate_interviews = DB::table('ins_interviews_calandar')
    ->leftJoin('ins_jobs', function($join)
    {
       $join->on('ins_interviews_calandar.job_id', '=', 'ins_jobs.id');
   })
    ->leftJoin('ins_job_category', function($join)
    {
       $join->on('ins_job_category.id', '=', 'ins_jobs.job_category_id');
   })
    ->where('ins_interviews_calandar.status','=',1)
    ->where('ins_interviews_calandar.candidate_id','=', $userID)
    ->orderBy('ins_interviews_calandar.interview_date','desc')
    ->select(['ins_interviews_calandar.*',
        'ins_jobs.state','ins_jobs.location',
        'ins_jobs.job_title',
        'ins_job_category.category_name'
        ])->limit(3)->get();

    return $get_candidate_interviews;
  }

  public function getCandidateInterviewDates() {
    $userID = Auth::id();
    $get_candidate_interviews_dates = DB::table('ins_interviews_calandar')
    ->where('candidate_id', '=', $userID)
    ->where('status', '=',1)
    ->select(['interview_date'])->get();
    return $get_candidate_interviews_dates;
  }

  public function deleteCandidateJob($job_id) {
    $userID = Auth::id();
    $now = Carbon::now();
    $deleteCandidateJob = DB::table('ins_job_candidate')
    ->where('candidate_id', '=', $userID)
    ->where('job_id', '=', $job_id)
    ->update(['deleted_at' => $now]);
    return $deleteCandidateJob;
  }

  public function getCandidatePendingDates() {
    $userID = Auth::id();
    $get_candidate_pending_dates = DB::table('ins_interview_pending_dates')
    ->leftJoin('interview_pending_candidate', function($join) {
       $join->on('interview_pending_candidate.job_id', '=', 'ins_interview_pending_dates.job_id');})
    ->where('interview_pending_candidate.candidate_id' ,'=', $userID)
    ->select(['ins_interview_pending_dates.*'])->groupBy('ins_interview_pending_dates.interview_dates')->get();
    return $get_candidate_pending_dates;
  }

  public function getPendingDate($id) {
    $dates = DB::table('ins_interview_pending_dates')->where('id', '=', $id)
    ->select(['ins_interview_pending_dates.*'])->first();
    return $dates;
  }

  public function getPendingInterview($id, $job_id) {
    $userID = Auth::id();
    $get_candidate_pending_dates = DB::table('interview_pending_candidate')
    ->leftJoin('ins_interview_pending_dates', function($join) {
       $join->on('interview_pending_candidate.job_id', '=', 'ins_interview_pending_dates.job_id');
    })
    ->where('interview_pending_candidate.candidate_id', '=', $userID)
    ->where('ins_interview_pending_dates.job_id', '=', $job_id)
    ->select(['ins_interview_pending_dates.interview_dates','ins_interview_pending_dates.interview_timings',
        'ins_interview_pending_dates.time','ins_interview_pending_dates.id'])->groupBy('ins_interview_pending_dates.interview_timings')->get();
    return $get_candidate_pending_dates;
  }

  public function getInterviewJobId($date) {
    return  DB::table('ins_interview_pending_dates')
    ->where('interview_dates', '=', $date)
    ->select(['ins_interview_pending_dates.job_id'])->groupBy('ins_interview_pending_dates.job_id')->get();
  }

  public function getInterviewTimings($job_id, $date) {
    return  DB::table('ins_interview_pending_dates')
    ->where('job_id', '=', $job_id)
    ->where('interview_dates', '=', $date)
    ->select(['ins_interview_pending_dates.interview_timings','ins_interview_pending_dates.job_id'])->get();
  }

  public function getInterviewPendingCandidate($job_id) {
    $userID = Auth::id();
    return  DB::table('interview_pending_candidate')->where('job_id', '=', $job_id)
    ->where('candidate_id', '=', $userID)
    ->get(); 
  }

  public function deleteInterviewPendingCandidate($job_id) {
    $userID = Auth::id();
    return  DB::table('interview_pending_candidate')->where('job_id', '=', $job_id)
    ->where('candidate_id', '=', $userID)
    ->delete();
  }

  public function deleteInterviewPendingDates($job_id, $timings) { 
    return DB::table('ins_interview_pending_dates')->where('job_id', '=', $job_id)
    ->where('interview_timings', '=', $timings)
    ->delete();
  }

  public function insertInterviewConfirmation($details, $status, $comment) {
    $userID = Auth::id();
    $interview_confirmation = new InterviewConfirmation();
    $interview_confirmation->job_id=  $details->job_id;
    $interview_confirmation->candidate_id= $userID;
    $interview_confirmation->interview_date= $details->interview_dates;
    $interview_confirmation->interview_time= $details->interview_timings;
    $interview_confirmation->interview_minutes= $details->time;
    $interview_confirmation->comment= $comment;
    $interview_confirmation->status= $status;
    $interview_confirmation->save();
    $id = $interview_confirmation->id;
    return $id;
  }

  public function getInterviewDet($id) {
    return DB::table('ins_interviews_calandar')->where('id', '=', $id)
    ->select(['ins_interviews_calandar.*'])
    ->first();
  }

  public function getPendingCandidateInterview() {
    $userID = Auth::id();
    return DB::table('interview_pending_candidate')
    ->leftJoin('ins_interview_pending_dates', function($join) {
      $join->on('interview_pending_candidate.job_id', '=', 'ins_interview_pending_dates.job_id');})
    ->leftJoin('ins_jobs', function($join) {
      $join->on('ins_jobs.id', '=', 'ins_interview_pending_dates.job_id');
    }) 
    ->where('interview_pending_candidate.candidate_id', '=', $userID)
    ->select(['ins_jobs.job_title','interview_pending_candidate.*','ins_interview_pending_dates.id'])
    ->groupBy('interview_pending_candidate.job_id')
    ->get(); 
  }

  public function getScheduleCandidateInterview() {
    $userID = Auth::id();
    return DB::table('ins_interviews_calandar')
    ->leftJoin('ins_jobs', function($join) {
      $join->on('ins_jobs.id', '=', 'ins_interviews_calandar.job_id');}) 
    ->leftJoin('ins_agency_details', function($join) {
      $join->on('ins_jobs.agency_id', '=', 'ins_agency_details.id');}) 
    ->where('ins_interviews_calandar.candidate_id', '=', $userID)
    ->where('ins_interviews_calandar.status', '=', 1)
    ->where('ins_agency_details.is_active' ,'1')
    ->select(['ins_jobs.id','ins_jobs.job_title','ins_jobs.prepared_by_email','ins_jobs.prepared_by_number','ins_jobs.salary_from','ins_jobs.prepared_by_name','ins_jobs.salary_to','ins_jobs.location','ins_agency_details.agency_name','ins_interviews_calandar.interview_date','ins_interviews_calandar.interview_time'])
    ->get();
  }

  public function getCompletedCandidateInterview() {
    $userID = Auth::id();
    return DB::table('ins_interviews_calandar')
    ->leftJoin('ins_jobs', function($join) {
      $join->on('ins_jobs.id', '=', 'ins_interviews_calandar.job_id');
    }) 
    ->leftJoin('ins_agency_details', function($join) {
      $join->on('ins_jobs.agency_id', '=', 'ins_agency_details.id');
    }) 
    ->where('ins_interviews_calandar.candidate_id', '=', $userID)
    ->where('ins_interviews_calandar.status', '=', 1)
    ->where('ins_interviews_calandar.interview_date', '<', date('Y-m-d'))
    ->where('ins_interviews_calandar.deleted_at', '=', NULL)
    ->where('ins_agency_details.is_active' ,'1')
    ->select(['ins_jobs.id','ins_jobs.job_title','ins_jobs.prepared_by_email','ins_jobs.prepared_by_number','ins_jobs.salary_from','ins_jobs.prepared_by_name','ins_jobs.salary_to','ins_jobs.location','ins_agency_details.agency_name','ins_interviews_calandar.interview_date','ins_interviews_calandar.interview_status','ins_interviews_calandar.interview_time'])->get();
  }

  public function getPendingInterJobID($id) {
    return DB::table('ins_interview_pending_dates')
    ->where('id','=', $id)
    ->select(['ins_interview_pending_dates.*'])->first();
  }

  public function chkInterviewCalandarDetails($timings, $date) {
    $userID = Auth::id();
    return DB::table('ins_interviews_calandar')
    ->where('interview_time','=', $timings)
    ->where('interview_date','=', $date)
    ->where('candidate_id','=', $userID)->exists();
  }

  public function getCandidateInterviewJobdetails($job_id) {
    return DB::table('ins_jobs')
    ->leftJoin('ins_interview_pending_dates', function($join) {
        $join->on('ins_jobs.id', '=', 'ins_interview_pending_dates.job_id');
    }) 
    ->where('ins_interview_pending_dates.job_id' ,'=', $job_id)
    ->select(['ins_jobs.job_title',
        'ins_jobs.suburb',
        'ins_interview_pending_dates.*'])
    ->first();
  }

  public function insertRssFeeds($item, $image) {
    $title = json_decode(json_encode($item->title),true);
    $link = json_decode(json_encode($item->link),true);
    $guid = json_decode(json_encode($item->guid),true);
    $date = json_decode(json_encode($item->pubDate),true);
    $feed_date = date("Y-m-d H:i:s", strtotime($date[0]));
    $exist = RssFeeds::where('guid' ,'=', $guid[0] )->first();
    if(empty($exist)) {
      $rss_feeds = new RssFeeds();
      $rss_feeds->title=  $title[0];
      $rss_feeds->link= $link[0];
      $rss_feeds->published= $feed_date;
      $rss_feeds->guid= $guid[0];
      $rss_feeds->thumbnail= $image[300];
      $rss_feeds->save();
    }
  }

  public function getRssFeeds() {
    return DB::table('ins_rss')->select(['ins_rss.*'])->get();
  }

  public function getUserTypeID() {
    $userID = Auth::id();
    return DB::table('users')->where('id', '=', $userID)->select(['type'])->first();
  }

  public function getJobMatchId($job_id) {
    $userID = Auth::id();
    return DB::table('ins_jobmatch')->where('candidate_id', '=', $userID)
          ->where('job_id', '=', $job_id)->select(['new_jobmatchedid'])->first();

  }

  public function getEmplyDetails() {
    $userID = Auth::id();
    return DB::table('ins_employees')->where('user_id', '=', $userID)->select(['ins_employees.*'])->first();

  }

  public function getClientDetails() {
    $userID = Auth::id();
    return DB::table('ins_clients')->where('user_id', '=', $userID)
    ->select(['ins_clients.email as new_personalemail ',
        'ins_clients.firstname as new_firstname ', 
        'ins_clients.surname as new_surname ',
        'ins_clients.phone_number as new_personalhomenumber',
        'ins_clients.mobile_number as new_personalmobilenumber'])->first();

  }

  public function getUserResume() {
    $userID = Auth::id();
    return DB::table('ins_cv')->select(['ins_cv.*','jct.category_name as category_name'])
    ->where('candidate_id', '=', $userID)
    ->leftJoin('ins_job_category as jct', function($join) {
      $join->on('jct.id', '=', 'ins_cv.category_id');
    })
    ->where('is_applied_resume', '=', 0)
    ->orderBy('id', 'desc')->limit(5)->where('status' ,'=',1)->get();
  }

  public function getUserCapbsPDF() {
    $userID = Auth::id();
    return DB::table('ins_pdf_usercapab')->where('user_id', '=', $userID)
    ->where('is_latest', '=',1)
    ->select(['name','id'])->first();
  }

  public function getUserCategory() {
    $userID = Auth::id();
    return DB::table('ins_user_job_category_types')->where('user_id', '=', $userID)
    ->leftJoin('ins_job_category', function($join) {
      $join->on('ins_job_category.id', '=', 'ins_user_job_category_types.job_category_type_id');
    })
    ->select(['ins_job_category.category_name as category_type_name','ins_user_job_category_types.job_category_type_id' ,'ins_user_job_category_types.pending' ])
    ->get();
  }

  public function getSkillassement() {
    $skills = DB::table('ins_skill_assement_types')
    ->leftJoin('ins_skill_assement_group', function($join) {
      $join->on('ins_skill_assement_types.skill_group_id', '=', 'ins_skill_assement_group.id');
    })->leftJoin('ins_user_skill_assesment', function($join) {
      $join->on('ins_user_skill_assesment.skill_asse_type_id', '=', 'ins_skill_assement_types.id');
    })
    ->select(['ins_skill_assement_types.id','ins_skill_assement_types.skil_names','ins_skill_assement_group.skill_group_name', DB::raw('CASE WHEN (select count(*) from ins_user_skill_assesment where ins_user_skill_assesment.skill_asse_type_id = ins_skill_assement_types.id) > 0 THEN 1 ELSE 0 END as status')
    ])
    ->orderBy('ins_skill_assement_group.skill_group_name')
    ->orderBy('skil_names', 'asc')
    ->groupBy('ins_skill_assement_types.id')
    ->where('ins_skill_assement_group.is_active' ,1)
    ->where('ins_skill_assement_types.is_active' ,1)
    ->get();
    return $skills;
  }

  public function getSkillAssessments($userId) {
    return DB::table('ins_user_skill_assesment')
        ->join('ins_skill_assement_types', 'ins_skill_assement_types.id', '=', 'ins_user_skill_assesment.skill_asse_type_id')
        ->join('ins_job_category', 'ins_job_category.id', '=', 'ins_skill_assement_types.job_category_id')
        ->where('candidate_id', '=', $userId)
        ->where('active', '=', 1)
        ->select('ins_user_skill_assesment.*',
            'ins_job_category.category_name as skill_group_name',
            'ins_job_category.id as skill_group_id',
            'skil_names')
        ->orderBy('skill_group_name', 'ASC')
        ->orderBy('skil_names', 'ASC')
        ->get();
}

  public function getSkillGroupList() {
    return DB::table('ins_job_category')->select(['ins_job_category.id','ins_job_category.category_name as skill_group_name'])->orderBy('skill_group_name', 'ASC')
      ->join('ins_skill_assement_types', 'ins_job_category.id', '=', 'ins_skill_assement_types.job_category_id')
    ->where('ins_job_category.is_active' ,'1')
    ->groupBy('ins_job_category.id')
    ->get();
  }


  public function getSkillList($userId) {
    return \Illuminate\Support\Facades\DB::table('ins_skill_assement_types')
    ->join('ins_job_category', 'ins_skill_assement_types.job_category_id', '=', 'ins_job_category.id')
    ->where('ins_job_category.is_active', '=', 1)
    ->where('ins_skill_assement_types.is_active', '=', 1)
    ->whereNotIn('ins_skill_assement_types.id', function ($query) use ($userId) {
    $query->select('skill_asse_type_id')->from('ins_user_skill_assesment')->where([
        'candidate_id' => $userId
    ]);
    })->select('ins_skill_assement_types.*')->get();
  }

  public function getLocation() {
   return DB::table('ins_locations')->whereNotIn('id', function($q) {
    $userID = Auth::id();
    $q->select('ins_location_id')->from('ins_user_job_locations')->where('ins_user_job_locations.user_id', '=', $userID);})
    ->select(['ins_locations.id','ins_locations.location'])
    ->where('is_active' ,'1')
    ->get();
  }

  public function getUserLocation() {
    $userID = Auth::id();
    return DB::table('ins_user_job_locations')->where('user_id', '=', $userID)
    ->leftJoin('ins_locations', function($join) {
      $join->on('ins_user_job_locations.ins_location_id', '=', 'ins_locations.id');
    })
    ->select(['ins_locations.location','ins_locations.id'])
    ->where('is_active' ,'1')
    ->get();
  }

  public function getAllAvailableCategories() {
    return DB::table('ins_job_category')->select(['ins_job_category.id', 'ins_job_category.category_name as category_type_name'])
        ->where('is_active', '1')->get();
  }

  public function getCategory() {
    return DB::table('ins_job_category')->whereNotIn('id', function($q) {
      $userID = Auth::id();
        $q->select('job_category_type_id')->from('ins_user_job_category_types')->where('user_id', '=', $userID);})
     ->select(['ins_job_category.id','ins_job_category.category_name as category_type_name'])
     ->where('is_active' ,'1')->get();
  }

  public function getCapabilities($job_id) {
    $userID = Auth::id();
    return DB::table('ins_capability')
    ->leftJoin('ins_capability_match_names', function($join) {
      $join->on('ins_capability_match_names.id', '=', 'ins_capability.capability_name_id');
    })
    ->leftJoin('ins_capability_group', function($join) {
      $join->on('ins_capability_match_names.group_id', '=', 'ins_capability_group.id');
    })
    ->leftJoin('ins_capability_level', function($join) {
      $join->on('ins_capability_level.level_id', '=', 'ins_capability.level_id');
    })->where('candidate_id', '=', $userID)->where('ins_capability.job_id', '=', $job_id)
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

  public function getUserInfo($candidate_id) {
    return DB::table('users')->where('id', '=', $candidate_id)
      ->select(['users.*'])->first();
  }

  public function getJobIsLatest() {
    $userID = Auth::id();
    $jobs = DB::select('SELECT * FROM (SELECT job_id,status FROM ins_cv WHERE candidate_id=? ORDER BY updated_at DESC) a GROUP BY job_id', [$userID]);
    return $jobs;
  }

  public function getcasemanagerEmail($systemuserid) {
    return  DB::table('ins_system_users')->where('systemuserid','=', $systemuserid)->select('internalemailaddress')->first();
  }

  public function updateJobMatchStatus($id, $status) {
    DB::table('ins_jobmatch')->where('id','=', $id) ->update(['match_status'=>$status]);
  }

  public function isjobeoirejected($job_id) {
    $userID = Auth::id();
    $jobs = DB::table('ins_job_candidate_eoi')
    ->where('ins_job_candidate_eoi.job_id' ,'=', $job_id)
    ->where('ins_job_candidate_eoi.candidate_id' ,'=', $userID)
    ->where('ins_job_candidate_eoi.ins_job_apply_id' ,'!=', '')
    ->where('ins_job_candidate_eoi.submit_status' ,'=', 121660006)
    ->count();
    return $jobs;
  }

  public function isjobapplied($job_id) {
    $userID = Auth::id();
    $jobs = DB::table('ins_job_candidate')
    ->where('ins_job_candidate.job_id' ,'=', $job_id)
    ->where('ins_job_candidate.candidate_id' ,'=', $userID)
    ->where('ins_job_candidate.submit_status' ,'=', 1)->count();
    return $jobs;
  }

  public function isjobeoi($job_id) {
    $userID = Auth::id();
    $jobs = DB::table('ins_jobmatch')
    ->where('job_id' ,'=', $job_id)
    ->where('candidate_id' ,'=', $userID)
    ->where('match_status' ,'=', 121660005)
    ->count();
    return $jobs;
  }

  public function isjobeoiapply($job_id) {
    $userID = Auth::id();
    $jobs = DB::table('ins_jobmatch')
    ->where('job_id' ,'=', $job_id)
    ->where('candidate_id' ,'=', $userID)
    ->where('match_status' ,'=', 121660015)
    ->count();
    return $jobs;
  }

  public function isjobdraft($job_id) {
    $userID = Auth::id();
    $jobs = DB::table('ins_job_candidate')
    ->where('ins_job_candidate.job_id' ,'=', $job_id)
    ->where('ins_job_candidate.candidate_id' ,'=', $userID)
    ->where('ins_job_candidate.submit_status' ,'=', 0)->count();
    return $jobs;
  }

  public function jobProgressStatus($job_id) {
    $userID = Auth::id();
    $jobs = DB::table('ins_job_candidate')
    ->where('ins_job_candidate.job_id' ,'=', $job_id)
    ->where('ins_job_candidate.candidate_id' ,'=', $userID)
    ->first();
    return $jobs;
  }

  public function isjobfromdraft($job_id) {
    $userID = Auth::id();
    $jobs = DB::table('ins_job_candidate')
    ->where('ins_job_candidate.job_id' ,'=', $job_id)
    ->where('ins_job_candidate.candidate_id' ,'=', $userID)
    ->where('ins_job_candidate.submit_status' ,'=', 2)->count();
    return $jobs;
  }

  public function getskillByJobID($job_id, $all = false) {
    if ($all) {
      $skills = [];
      $results = DB::table('ins_skillmatch_job')
          ->leftJoin('ins_skillmatch_names', 'ins_skillmatch_names.similar_id', '=', 'ins_skillmatch_job.skill_id')
          ->where('job_id','=', $job_id)
          ->select('ins_skillmatch_job.skill_id', 'ins_skillmatch_names.id')
          ->orderBy('status', 'ASC')
          ->get();
      foreach ($results as $result) {
        if (! empty($result->id)) {
          $skills[$result->id] = (object)['skill_id' => $result->id];
        }
        $skills[$result->skill_id] = (object)['skill_id' => $result->skill_id];
      }
      return (object)$skills;
    }
    return DB::table('ins_skillmatch_job')->where('job_id','=', $job_id)
      ->where('status','=',1)->select('ins_skillmatch_job.skill_id')->get();
  }

  public function getSkillNamesByJob($jobId) {
    return DB::table('ins_skillmatch_job')
        ->join('ins_skillmatch_names', 'ins_skillmatch_names.id', '=', 'ins_skillmatch_job.skill_id')
        ->where('job_id','=', $jobId)
        ->select('ins_skillmatch_names.*')
        ->orderBy('status', 'ASC')
        ->get();
  }

  public function getskillByCandidate($resumeId = null) {
    $userID = Auth::id();
    if ($resumeId) {
      return DB::table('ins_skill_candidate')->where('candidate_id','=', $userID)->where('resume_id', '=', $resumeId)->select('ins_skill_candidate.skill_id')->get();
    }
    return DB::table('ins_skill_candidate')->where('candidate_id','=', $userID)->select('ins_skill_candidate.skill_id')->get();
  }

  public function insertSkillMatch($userId, $skillId, $jobId, $status) {
    $skill_match = new SkillMatch();
    $skill_match->job_id = $jobId;
    $skill_match->candidate_id = $userId;
    $skill_match->skill_id = $skillId;
    $skill_match->status = $status;
    $skill_match->save();
  }

  public function getSkillMatchCandidate($job_id, $matchOnly = false) {
    $userID = Auth::id();
    $r = DB::table('ins_skillmatch')
    ->leftJoin('ins_skillmatch_names', function($join) {
      $userID = Auth::id();
      $join->on('ins_skillmatch_names.id', '=', 'ins_skillmatch.skill_id');
    })
    ->where('job_id','=', $job_id)->where('candidate_id','=', $userID)
    ->select('ins_skillmatch.*','ins_skillmatch_names.skill_name');
    if ($matchOnly) {
      $r->where('status', '=', 1);
    }
    return $r->get();
  }

  public function getSkillGap($userId) {
    return DB::table('ins_skillmatch')
        ->join('ins_skillmatch_names', 'ins_skillmatch_names.id', '=', 'ins_skillmatch.skill_id')
        ->where('candidate_id', '=', $userId)
        ->select(
            'ins_skillmatch.*',
            'ins_skillmatch_names.skill_name',
            DB::Raw('COUNT(ins_skillmatch.skill_id) AS gap')
        )
        ->groupBy('ins_skillmatch.skill_id')
        ->where('status', '=', 0)
        ->limit(20)
        ->orderBy('gap', 'DESC')
        ->orderBy('count', 'DESC')
        ->get();
  }

  public function downloadfile($id) {
    $url = DB::table('ins_role_desc_pdf')->where('id','=', $id)->where('is_latest','=', 1)->first();
    $resume_url=$url->url;
    $resume_url = explode("/", $resume_url);
    $config = Config::get('aws');
        $config['bucket'] = $config['stagingbuckets']['careerone'];
        $s3 = ExternalFileFactory::create('S3');
        $s3->open($config);
    $destpath = storage_path().'/download/';
    $name = $url->name;
        $stream = $s3->download($resume_url[7], $destpath,array('path' =>  'ins/role_description/' .$url->job_id), $name);
    $file = storage_path().'/download/'.$name;
    return \Response::download($file);
  }

  public function downloadfileWithError($id) {
    $url = DB::table('ins_role_desc_pdf')->where('id','=', $id)->where('is_latest','=', 1)->first();
    $resume_url=$url->url;
    $resume_url = explode("/", $resume_url);
    $config = Config::get('aws');
    $config['bucket'] = $config['stagingbuckets']['careerone'];
    $s3 = ExternalFileFactory::create('S3');
    $s3->open($config);
    $destpath = storage_path().'/download/';
    $name = $url->name;
    $stream = $s3->download($resume_url[7], $destpath,array('path' =>  'ins/role_description/' .$url->job_id), $name);
    $file = storage_path().'/download/'.$name;
    $data['status'] = false;;
    $data['data'] = '';
    if (\File::exists($file)) {
      $data['data'] =  \Response::download($file);
      $data['status'] = true;;
      return $data;
    } else {
      $data['data'] = 'File does not exist';
      $data['status'] = false;;
      return $data;
    }
  }

  public function getCandidateJobs($userId) {
    return DB::table('ins_jobmatch')
      ->join('ins_jobs', 'ins_jobs.id', '=', 'ins_jobmatch.job_id')
      ->select('ins_jobmatch.*', 'ins_jobs.job_title')
      ->where('candidate_id','=', $userId)
      ->groupBy('job_id')
      ->get();
  }
}
