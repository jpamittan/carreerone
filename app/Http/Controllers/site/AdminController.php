<?php

namespace App\Http\Controllers\site;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Entities\User;
use App\Models\Services\JobAssetService;
use App\Models\Services\Purifier;
use App\Models\Services\JsxService;
use App\Models\Services\SearchLocationService;
use App\Models\Services\LocationService;
use App\Models\Repositories\ResumeRepository;
use App\Models\Repositories\CapabilityMatchRepository;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Pagination\Paginator;
use DB, View, Carbon, Session, Validator, Response, Redirect;

class AdminController extends Controller {
    protected $featuredJob = null;
    protected $jsx = null;
    protected $category = null;
    protected $searchLocation = null;
    protected $indeed = null;
    protected $geoLocation = null;
    protected $jobAlert = null;
    protected $ghosting = null;
    protected $source = null;

    public function __construct(JsxService $jsx, SearchLocationService $searchLocation, LocationService $location) {
        $this->jsx = $jsx;
        $this->searchLocation = $searchLocation;
        $this->location = $location;
        parent::__construct();
    }

    public function fileUpload($job_id) {
        $input = Input::all();
        $resumeRep = new ResumeRepository();
        $status = $input['status'];
        $service = new JobAssetService();
        $cover_letter_id = null ; 
        $supporting_docs_id = null ; 
        if(isset($input['coveringletter'])){
          $covering_letter = $input['coveringletter'];
          $cover_letter_id = $resumeRep->coveringLetterUpload($covering_letter,$job_id);
        }
        if(isset($input['supporting_file'])){
          $supporting_file = $input['supporting_file'];
          $supporting_docs_id = $resumeRep->supportingDocsUpload($supporting_file,$job_id);
        }
        if(!isset($input['resume']) || isset($input['inputfile']) ) {
            if (! isset($input['inputfile']) || empty($input['inputfile'])) {
                return Redirect::back()->with('message', trans('messages.apply.failed_resume'));
            } else if (!in_array(pathinfo($input['inputfile']->getClientOriginalName(),PATHINFO_EXTENSION),array('doc','docx','pdf','rtf','txt'))) {
                return Redirect::back()->with('message', trans('messages.apply.resume_format'));
            } else if ($input['inputfile']->getSize() > 500000) {
                return Redirect::back()->with('message', trans('messages.apply.resume_size'));
            } else {
                $file = $input['inputfile'];
                $resumeRep = new ResumeRepository();
                $resumeInfo = $resumeRep->fileUpload($file,$job_id,1,0,true,null,null,$is_applied_resume =1);
                return Redirect::to('site/dashboard')->with('message', trans('messages.apply.success'));
            }
        } else {
            $input['resume_id']=  $input['resume'];
            $resumeRep = new ResumeRepository();
            $resume_id = $resumeRep->cvID($input['resume_id'],$job_id,false,$status,null,false,$cover_letter_id,$supporting_docs_id);
            if($status == 0 ){
                $msg = trans('messages.apply.draft');
            } else {
                $msg = trans('messages.apply.success');
            }
            return Redirect::to('site/dashboard')->with('message', $msg);
        }
    }

    public function dashboard() {
        $checkprofile = $this->checkProfile();
        $checkskills = $this->checkSkills();
        if (($checkprofile != 'true')){
            $msg = $checkprofile;
            return Redirect::to('site/profile')->with('error', $msg);
        }
        if($checkskills < 10){
            if(10 - $checkskills == 1) {
                $msg = 'You must have a minimum of 10 skills defined. Please add 1 more skill to Skill Summary before continuing.';
            } else {
                $msg = 'You must have a minimum of 10 skills defined. Please add ' . (10 - $checkskills) . ' more skills to Skill Summary before continuing.';
            }
            return Redirect::to('site/profile')->with('error', $msg);
        }
        $params = Input::all();
        $service = new JobAssetService();
        $job_status = $service->getJobstatus();
        $apply_history = $service->applyHistory();
        $candidate_interviews = $service->getCandidateInterview();
        $rss_feeds = $service->getRssFeeds();
        $job_latest = $service->getJobIsLatest();
        $careeronejobs = $this->getCareeronejobs($params);
        $careeronejobs['params']['jobtype'] = 'CareerOne Roles ';
        $careeronejobsdata = [
            'jobCount' => $careeronejobs['jobCount'],
            'jobs' => $careeronejobs['jobs'],
            'params' => $careeronejobs['params'],
            'fullparams' => $careeronejobs['fullparams'],
            'paginator' => $careeronejobs['paginator'],
            'query' => $careeronejobs['query'],
        ];
        $insmatchedjobs = $this->getInsmatchedjobs($params);
        $insmatchedjobs['params']['jobtype'] = 'Application Monitor';
        $insmatchedalljobs = [
            'jobCount' => $insmatchedjobs['jobCount'],
            'jobs' => $insmatchedjobs['jobs'],
            'params' => $insmatchedjobs['params'],
            'fullparams' => $insmatchedjobs['fullparams'],
            'paginator' => $insmatchedjobs['paginator'],
            'query' => $insmatchedjobs['query'],
        ];
        $notinmatchids = array();
        if (!empty($insmatchedjobs['jobs'])) {
            foreach ($insmatchedjobs['jobs'] as $j) {
                $notinmatchids[] = $j->id;
            }
        }
        $insjobs = $this->getInsjobs($params, $notinmatchids);
        $insjobs['params']['jobtype'] = 'Mobility Pathway Roles ';
        $insalljobs = [
            'jobCount' => $insjobs['jobCount'],
            'jobs' => $insjobs['jobs'],
            'params' => $insjobs['params'],
            'fullparams' => $insjobs['fullparams'],
            'paginator' => $insjobs['paginator'],
            'query' => $insjobs['query'],
        ];
        $notinmatchids1 = array();
        if (!empty($insjobs['jobs'])) {
            foreach ($insjobs['jobs'] as $j) {
                $notinmatchids1[] = $j->id;
            }
        }
        $notinmatchidshistory = array_merge($notinmatchids, $notinmatchids1);
        $expiredJobsCurrentPage = !empty(request()->get('mh_page')) ? request()->get('mh_page') : 1;
        $expiredJobHistory = $service->getMatchedJobHistory(true, $expiredJobsCurrentPage - 1);
        $inshistoryjobs['params']['jobtype'] = 'Match History ';
        $inshistoryalljobs = [
            'jobCount' => count($expiredJobHistory),
            'jobs' => $expiredJobHistory,
            'params' => $inshistoryjobs['params'],
            'fullparams' => [],
            'paginator' => [],
            'query' => [],
        ];
        $futureJobsCurrentPage = !empty(request()->get('fj_page')) ?request()->get('fj_page') : 1;
        $futureJobs = $service->getMatchedJobHistory(false, $futureJobsCurrentPage - 1);
        $insfuturejobs['params']['jobtype'] = 'Future Roles';
        $insfuturealljobs = [
            'jobCount' => count($futureJobs),
            'jobs' => $futureJobs,
            'params' => $insfuturejobs['params'],
            'fullparams' => [],
            'paginator' => [],
            'query' => [],
        ];
        return View::make('site.home.dashboard', array(
            'jobs' => array(),
            'job_status' => $job_status,
            'job_latest' => $job_latest,
            'careeronealljobs' => $careeronejobsdata,
            'insalljobs' => $insalljobs,
            'insmatchedjobs' => $insmatchedalljobs,
            'inshistoryalljobs' => $inshistoryalljobs,
            'insfuturealljobs' => $insfuturealljobs,
            'candidate_interview' => $candidate_interviews,
            'rss_feeds' => $rss_feeds,
            'apply_history' => $apply_history,
        ));
    }

    public function dashboardPage() {
        $bln = true;
        $sortby = Input::get('sortby');
        $service = new JobAssetService();
        $job_latest = $service->getJobIsLatest();
        if($sortby == "date_posted") {
            $jobs = $service->getJobsPosted();
        } else {
            $jobs = $service->getJobsExpiring();
        }
        return View::make('site.partials.dashboardpage', array(
            'jobs' => $jobs,
            'bln' => $bln,
            'job_latest' =>$job_latest,
            )
        )->render();
    }

    public function applyHistoryPage() {
        $service = new JobAssetService();
        $apply_history = $service->applyHistory();
        $job_latest = $service->getJobIsLatest();
        return View::make('site.partials.applyhistorypage', array(
            'apply_history' => $apply_history,
            'job_latest' =>$job_latest,
            )
        )->render();
    }

    public function profile(Request $request) {
        $profileError = $this->checkProfile();
        if(!empty($profileError) && $profileError != "true") {
            session()->flash('error', $profileError);
        }
        $category_popup = $this->checkPopupCateogry();
        $service = new JobAssetService();
        $candidate_interviews = $service->getCandidateInterview();
        $rss_feeds = $service->getRssFeeds();
        $profile_det = $service->getProfileDetails();
        $user_resume = $service->getUserResume();
        $cap_match_repo = new CapabilityMatchRepository();
        $u = new \stdClass;
        $u->candidate_id = Auth::id();
        $user_capabilities = $cap_match_repo->getCandidateCapabilitiesCriteria($u);
        $user_jobs = $service->getCandidateJobs($u->candidate_id);
        $user_capabilities = $service->getMismatches($user_capabilities, $user_jobs);
        $skillGap = $service->getSkillGap(Auth::id());
        $category = $service->getCategory();
        $allAvailableCategories = $service->getAllAvailableCategories();
        $user_category = $service->getUserCategory();
        $skillGap_chunk = null;
        if (!empty($skillGap)) {
            $skillGap_chunk = array_chunk($skillGap, ceil(count($skillGap) / 2));
        }
        $skills = $service->getSkillAssessments();
        $location = $service->getLocation();
        $user_location = $service->getUserLocation();
        $skillGroups = $service->getSkillGroupList();
        $skillList = $service->getSkillList();
        $user = $user = DB::table('users')->where('users.id', '=', Auth::id())->first();
        $frequency = [
            121660000 => 'Daily',
            121660001 => 'Weekly',
            121660002 => 'Monthly',
            121660003 => 'Quarterly',
            121660004 => 'Half yearly',
            121660005 => 'Annually',
            121660006 => 'Not used for 5-10 years',
            121660007 => 'Not used for 10+ years',
        ];
        $recency = [
            121660000 => 'Within last 12 months',
            121660001 => 'Within last 3 years',
            121660002 => 'Within last 5 years',
            121660003 => 'Not used for 5-10 years',
            121660004 => 'Not used for 10+ years',
        ];
        $level = [
            121660000 => 'Foundational',
            121660001 => 'Intermediate',
            121660002 => 'Adept',
            121660003 => 'Advanced',
            121660004 => 'Highly Advanced',
        ];
        return View('site.home.profile', array(
            'candidate_interview' => $candidate_interviews,
            'rss_feeds' => $rss_feeds,
            'profile_det' => $profile_det,
            'user_resume' => $user_resume,
            'user_category' => $user_category,
            'skills' => $skills,
            'location' => $location,
            'user_location' => $user_location,
            'category' => $category,
            'user_capabilities' => $user_capabilities,
            'category_popup' => $category_popup,
            'user_id' => $profile_det->employeeid,
            'skill_gap' => $skillGap_chunk,
            'skill_groups' => $skillGroups,
            'skill_list' => $skillList,
            'frequency' => $frequency,
            'recency' => $recency,
            'level' => $level,
            'user' => $user,
            'allAvailableCategories' => $allAvailableCategories
        ));
    }

    public function calandar() {
       return View('site.home.calandarb');
    }

    public function settings() {
       return View('site.home.settings');
    }

    public function confirmInterview() {
        $checkprofile =  $this->checkProfile() ;
        if($checkprofile != 'true') {
            $msg = $checkprofile;
            return Redirect::to('site/profile')->with('error', $msg);
        }
        $checkskills = $this->checkSkills();
        if($checkskills < 10) {
            if(10 - $checkskills == 1) {
                $msg = 'You must have a minimum of 10 skills defined. Please add 1 more skill to Skill Summary before continuing.';
            } else {
                $msg = 'You must have a minimum of 10 skills defined. Please add ' . (10 - $checkskills) . ' more skills to Skill Summary before continuing.';
            }
            return Redirect::to('site/profile')->with('error', $msg);
        } 
        $service = new JobAssetService();
        $apply_history = $service->applyHistory();
        $candidate_interviews = $service->getCandidateInterview();
        $pending_interview_candidate = $service->getPendingCandidateInterview();
        $schedule_interview_candidate = $service->getScheduleCandidateInterview();
        $completed_interview_candidate = $service->getCompletedCandidateInterview();
        $job_latest = $service->getJobIsLatest();
        $rss_feeds = $service->getRssFeeds();
        $params= array();
        $notinmatchids= array();
        $insjobs = $this->getInsjobs($params, $notinmatchids);
        $insjobs['params']['jobtype'] = 'Mobility Pathway Roles';
        $insalljobs =    [
            'jobCount' => $insjobs['jobCount'],
            'jobs' => $insjobs['jobs'],
            'params' => $insjobs['params'],
            'fullparams' => $insjobs['fullparams'],
            'paginator' => $insjobs['paginator'],
            'query' => $insjobs['query'],
        ];
        return View('site.home.comfirminterview', array(
            'apply_history' => $apply_history,
            'candidate_interview' => $candidate_interviews,
            'pending_interview_candidate' => $pending_interview_candidate,
            'schedule_interview_candidate' => $schedule_interview_candidate,
            'completed_interview_candidate' => $completed_interview_candidate,
            'rss_feeds' =>$rss_feeds,
            'job_latest' =>$job_latest,
            'insalljobs' =>$insalljobs,
        ));
    }

    public function ScheduledDetails($job_id) {
        $service = new JobAssetService();
        $getJob = $service->getJob($job_id);
        $getskills =  $service->getskills($job_id);
        if(isset($getJob)){
            return View::make('site.home.scheduledview', array(
                'job' => $getJob,
                'skill_match' =>$getskills,
            ));
        } else {
            return Redirect::route('site-nojob');
        }
    }

    public function CompletedInterviewDetails($job_id) {
        $service = new JobAssetService();
        $getJob = $service->getJob($job_id);
        $getskills =  $service->getskills($job_id);
        if(isset($getJob)){
            return View::make('site.home.scheduledview', array(
                'job' => $getJob,
                'skill_match' =>$getskills,
            ));
        } else {
            return Redirect::route('site-nojob');
        }
    }
    
    public function rssFeeds() {
        $url ="http://inscm.com.au/feed/";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $html = curl_exec($ch);
        curl_close($ch);
        $rss = simplexml_load_string($html);
        foreach ($rss->channel->item as $item) {
            $image = $this->getImageUrl($item->description);
            $title = json_decode(json_encode((array)$item->title));
            $service = new JobAssetService();
            $service->insertRssFeeds($item,$image);
        }
    }

    public function getImageUrl($desc){
        $arr = array();
        $mystring = $desc;
        $findme   = 'srcset';
        $pos = strpos($mystring, $findme);
        if ($pos === false) {
           return $img;
        } else {
            $str = substr($desc , $pos+ 8 , strlen($desc));
            $mystring = $str;
            $findme   = '" ';
            $pos1 = strpos($mystring, $findme);
            if ($pos1 === false) {
                return $img;
            } else {
                $finalstr = substr($desc, $pos +8, $pos1);
                $imgs = explode(", ", $finalstr);
                if(count($imgs) > 0 ){
                    foreach($imgs as $img) {
                        $im = explode(" ", $img);
                        $size = str_replace("w", "", @$im[1]);
                        $arr[$size] = @$im[0];
                    }
                }
            }
        }
        return $arr;
    }

    public function getInsjobs($params = array() , $notinmatchids = array()) {
        $service = new JobAssetService();
        $page = (int) (isset($params['page']) ? $params['page'] : 1);
        $cpage = (int) (isset($params['page']) ? $params['page'] : 1);
        $page = ($page > 50) ? 50 : $page;
        $alljobs_arr = $service->getJobDetails($params, $page, 20 ,$type='ins' , $notinmatchids);
        $alljobs = $alljobs_arr['result'];
        $job_status = $service->getJobstatus();
        $fullparams='';
        foreach($params as $k=>$v) {
            if($k == 'page' || $k == 'location_id'){}else{ 
                $fullparams .= $k.'='.$v."&";
            }
        }
        $jobCount = $alljobs_arr['count'];
        if ($jobCount != 1000) {
            $page = ceil(count($alljobs) / 20);
            $page = ($page < 0) ? 1 : $page;
            $jobCount = ($jobCount > 1000) ? 1000 : $jobCount;
            $jobCount = ($jobCount < 0) ? 0 : $jobCount;
        }
        $paginator  = null;
        if (!empty($alljobs)) {
            $paginator = new Paginator([], 20, $page ,['total' => $jobCount, 'currentpage' => $cpage , 'perpage' => 20]);
        }
        $view = [
            'jobCount' => $jobCount,
            'jobs' => isset($alljobs) ? $alljobs : [],
            'params' => $params,
            'fullparams' => $fullparams,
            'paginator' => $paginator,
            'query' => ''
        ];
        return $view;
    }

    public function getInsHistoryJobs($params = array() , $notinmatchids = array()) {
        $service = new JobAssetService();
        $page = (int) (isset($params['page']) ? $params['page'] : 1);
        $cpage = (int) (isset($params['page']) ? $params['page'] : 1);
        $page = ($page > 50) ? 50 : $page;
        $alljobs_arr = $service->getHistoryJobs($params, $page, 20 ,$type='ins' , $notinmatchids);
        $alljobs = $alljobs_arr['result'];
        $job_status = $service->getJobstatus();
        $fullparams='';
        foreach($params as $k=>$v){
            if($k == 'page' || $k == 'location_id'){}else{ 
                $fullparams .= $k.'='.$v."&";
            }
        }
        $jobCount = $alljobs_arr['count'];
        if ($jobCount != 1000) {
            $page = ceil(count($alljobs) / 20);
            $page = ($page < 0) ? 1 : $page;
            $jobCount = ($jobCount > 1000) ? 1000 : $jobCount;
            $jobCount = ($jobCount < 0) ? 0 : $jobCount;
        }
        $paginator  = null;
        if (!empty($alljobs)) {
            $paginator = new Paginator([], 20, $page, ['total' => $jobCount, 'currentpage' => $cpage, 'perpage' => 20]);
        }
        $view = [
            'jobCount' => $jobCount,
            'jobs' => isset($alljobs) ? $alljobs : [],
            'params' => $params,
            'fullparams' => $fullparams,
            'paginator' => $paginator,
            'query' => ''
        ];
        return $view;
    }

    public function getInsmatchedjobs($params = array()) {
        $service = new JobAssetService();
        $page = (int) (isset($params['page']) ? $params['page'] : 1);
        $cpage = (int) (isset($params['page']) ? $params['page'] : 1);
        $page = ($page > 50) ? 50 : $page;
        $alljobs_arr = $service->getMatchedJobs($params, $page, 20 , $type='matched');
        $alljobs = $alljobs_arr['result'];
        $tempjobs = array();
        foreach($alljobs as $j){
            $job_id = $j->id; 
            $isjobeoi = $service->isjobeoi($job_id);
            $isjobeoiapply = $service->isjobeoiapply($job_id);
            $isjobeoirejected = $service->isjobeoirejected($job_id);
            $isjobapplied = $service->isjobapplied($job_id);
            $isjobdraft = 0;
            $progressstatus=0;
            $jobprogressstatus = $service->jobProgressStatus($job_id);
            if(!empty($jobprogressstatus)){
                $progressstatus=$jobprogressstatus->ins_progress;
            }
            $j->job_apply = $isjobapplied;
            $j->job_draft =  $isjobdraft;
            $j->job_eoi= $isjobeoi;
            $j->isjobeoiapply= $isjobeoiapply;
            $j->job_eoi_rejected= $isjobeoirejected;
            $j->jobprogressstatus= $progressstatus;
            if($isjobdraft == 0 && $isjobeoi == 0 && $isjobeoiapply == 0 && $isjobeoirejected == 0){
                $tempjobs[] = $j;
            } else {
                $tempjobs[] = $j;
            }
        } 
        $alljobs = $tempjobs;
        $alljobs_arr['count'] = count($alljobs);
        $job_status = $service->getJobstatus();
        $fullparams='';
        foreach($params as $k=>$v){
            if($k == 'page' || $k == 'location_id'){}else{ 
                $fullparams .= $k.'='.$v."&";
            }
        }
        $jobCount = $alljobs_arr['count'];
        if ($jobCount != 1000) {
            $page = ceil(count($alljobs) / 20);
            $page = ($page < 0) ? 1 : $page;
            $jobCount = ($jobCount > 1000) ? 1000 : $jobCount;
            $jobCount = ($jobCount < 0) ? 0 : $jobCount;
        }
        $paginator  = null;
        if (!empty($alljobs )  ) {
            $paginator = new Paginator([], 20, $page ,['total' => $jobCount, 'currentpage' => $cpage , 'perpage' => 20]);
        }
        $view = [
            'jobCount' => $jobCount,
            'jobs' => isset($alljobs) ? $alljobs : [],
            'params' => $params,
            'fullparams' => $fullparams,
            'paginator' => $paginator,
            'query' => ''
        ];
        return $view;
    }
 
    public function getInsFuturejobs($params = array(), $notinmatchids = array()) {
        $service = new JobAssetService();
        $page = (int) (isset($params['page']) ? $params['page'] : 1);
        $cpage = (int) (isset($params['page']) ? $params['page'] : 1);
        $page = ($page > 50) ? 50 : $page;
        $alljobs_arr = $service->getFutureJobs($params, $page, 20 , $type='ins', $notinmatchids);
        $alljobs = $alljobs_arr['result'];
        $tempjobs = array();
        foreach($alljobs as $j){
            $job_id = $j->id; 
            $isjobeoi = $service->isjobeoi($job_id);
            $isjobeoiapply = $service->isjobeoiapply($job_id);
            $isjobeoirejected = $service->isjobeoirejected($job_id);
            $isjobapplied = $service->isjobapplied($job_id);
            $isjobdraft = 0;
            $progressstatus=0;
            $jobprogressstatus = $service->jobProgressStatus($job_id);
            if(!empty($jobprogressstatus)){
                $progressstatus=$jobprogressstatus->ins_progress;
            }
            $j->job_apply = $isjobapplied;
            $j->job_draft =  $isjobdraft;
            $j->job_eoi= $isjobeoi;
            $j->isjobeoiapply= $isjobeoiapply;
            $j->job_eoi_rejected= $isjobeoirejected;
            $j->jobprogressstatus= $progressstatus;
            if(
                $isjobdraft == 0 && 
                $isjobeoi == 0 && 
                $isjobeoiapply == 0 && 
                $isjobeoirejected == 0
            ) {
                $tempjobs[] = $j;
            } else {
              $tempjobs[] = $j;
            }
        } 
        $alljobs = $tempjobs;
        $alljobs_arr['count'] = count($alljobs);
        $job_status = $service->getJobstatus();
        $fullparams='';
        foreach($params as $k=>$v){
            if($k == 'page' || $k == 'location_id'){}else{ 
                $fullparams .= $k.'='.$v."&";
            }
        }
        $jobCount = $alljobs_arr['count'];
        if ($jobCount != 1000) {
            $page = ceil(count($alljobs) / 20);
            $page = ($page < 0) ? 1 : $page;
            $jobCount = ($jobCount > 1000) ? 1000 : $jobCount;
            $jobCount = ($jobCount < 0) ? 0 : $jobCount;
        }
        $paginator  = null;
        if (!empty($alljobs )  ) {
            $paginator = new Paginator([], 20, $page ,['total' => $jobCount, 'currentpage' => $cpage , 'perpage' => 20]);
        }
         $view = [
            'jobCount' => $jobCount,
            'jobs' => isset($alljobs) ? $alljobs : [],
            'params' => $params,
            'fullparams' => $fullparams,
            'paginator' => $paginator,
            'query' => ''
        ];
        return $view;
    }

    public function getCareeronejobs($params = array()){
        $page = (int) Input::get('page', 1);
        $cpage = (int) Input::get('page', 1);
        $page = ($page > 50) ? 50 : $page;
        $search_location_get =  Input::get('search_location');
        $serviceJobAssetService = new JobAssetService();
        $slocation = $serviceJobAssetService->getLocation();
        $user_location = $serviceJobAssetService->getUserLocation();
        if (empty($search_location_get)) {
            if ($search_location_get != '' && !isset($params['search_location'])) {
                if(!empty($user_location)){
                    $params['search_location'] = $user_location[0]->location;
                }
            }
        }
        $jobs = $this->jsx->applyParams($params)->setPage($page)->search();
        $query = $this->jsx->getQuery();
        $locationId = $this->jsx->getLocationId();
        $locationName = $this->jsx->getLocationName();
        $params['location_id'] = $locationId;
        $jobCount = $jobs['Jobs']['@attributes']['Found'];
        if (empty($search_location_get)) {
            if ($search_location_get != '' && !isset($params['search_location'])) {
                if (!empty($user_location)) {
                    $params['search_location'] = @$user_location[0]->location;
                }
            }
        }
        if ($jobCount != 1000) {
            $page = ceil($jobs['Jobs']['@attributes']['Found'] / 20);
            $page = ($page < 0) ? 1 : $page;
            $jobCount = ($jobCount > 1000) ? 1000 : $jobCount;
            $jobCount = ($jobCount < 0) ? 0 : $jobCount;
        }
        $paginator  = null;
        $alljobs =array();
        if (!empty($jobs) && !empty($jobs['Jobs']) && !empty($jobs['Jobs']['Job'])) {
            $alljobs =$jobs['Jobs']['Job'];
        }
        if (isset($jobs['Jobs']['Job'])  ) {
            $paginator = new Paginator([], 20, $page ,['total' => $jobCount, 'currentpage' => $cpage , 'perpage' => 20] );
        }
        $fullparams = '';
        foreach($params as $k=>$v){
            if($k == 'page' || $k == 'location_id' || $k == 'ctabval'){}else{ 
                $fullparams .= $k.'='.$v."&";
            }
        }
        $view = [
            'jobCount' => $jobCount,
            'jobs' => isset($alljobs) ? $alljobs : [],
            'params' => $params,
            'fullparams' => $fullparams,
            'paginator' => $paginator,
            'query' => $query
        ];
        return $view;
    }
}
