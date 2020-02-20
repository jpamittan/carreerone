<?php
namespace App\Http\Controllers\site;

use App\Http\Requests;
use App\Http\Controllers\site\AdminController;
use App\Models\Containers\ResumeExtract;
use App\Models\Entities\Jobs;
use App\Models\Entities\Employee;
use App\Models\Repositories\CapabilityMatchRepository;
use App\Models\Repositories\EmailRepository;
use App\Models\Repositories\JobRepository;
use App\Models\Repositories\PushJobCapabilityRepository;
use App\Models\Repositories\PushUserCapabilityRepository;
use App\Models\Repositories\ResumeRepository;
use App\Models\Repositories\UserPushRepository;
use App\Models\Services\EmailService;
use App\Models\Services\JobAssetService;
use App\Models\Services\ProfileAssetService;
use App\Models\Services\PushUserCapability;
use App\Models\Services\PushJobCapability;
use App\Models\Services\PushJobmatchStatus;
use App\Models\Services\Purifier;
use App\Models\Gateways\RedactGateway;
use App\Models\Proxies\FileProxy;
use App\Models\Gateways\Redact\ResumeRedact;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Config, DB, View, Redirect, Response;

class ProfileController extends AdminController {
    public function location($keywords) {
        $service = new ProfileAssetService();
        $location = $service->getLocation($keywords);
        return Response::json(['location' => $location]);
    }

    public function category($keywords) {
        $service = new ProfileAssetService();
        $category = $service->getCategory($keywords);
        return Response::json(['category' => $category]);
    }

    public function postLocation() {
        $service = new ProfileAssetService();
        $input = Input::all();
        $getlocation = $service->getLocation();
        $count_location = $getlocation->count;
        if(!isset($input['location']) || empty($input['location'])) {
            return trans('messages.apply.location_profile');
        } else if ($count_location >= 4) {
            return trans('messages.apply.location_more');
        } else {
            $location = $service->postLocation($input['location']);
            $userID = Auth::id();
            if(!empty($userID)){
                $loc = new UserPushRepository();
                $loc->pushEmployeesLocations($userID);
            }
            $service = new JobAssetService();
            $location = $service->getLocation();
            $user_location = $service->getUserLocation();
            return View('site.partials.location', compact('location','user_location'));
        }
        $service = new ProfileAssetService();
        $category = $service->postLocation();
    }

    public function userResumeUpload() {
        $input = Input::all();
        if(!isset($input['inputfile']) || empty($input['inputfile'])) {
            return trans('messages.apply.failed_resume');
        } else if(! in_array(pathinfo($input['inputfile']->getClientOriginalName(), PATHINFO_EXTENSION), array('doc', 'docx', 'pdf', 'rtf', 'txt'))) {
            return trans('messages.apply.resume_format');
        } else if ($input['inputfile']->getSize() > 5000000) {
            return trans('messages.apply.resume_size');
        } else {
            $resumeRep = new ResumeRepository();
            $service = new JobAssetService();
            $id = $resumeRep->fileUpload($input['inputfile'], $job_id=0, $status=1, $input['category_resume'], $cvemail=true, $cover_letter_id=null, $supporting_docs_id=null);
            $resume = DB::table('ins_cv')->where('id', '=', $id)->first();
            $user = DB::table('users')->where('users.id', '=', Auth::id())->first();
            $service->uploadToMonster($resume, $user, $input['category_resume']);
            $user_resume = $service->getUserResume();
            $category = $service->getCategory();
            $allAvailableCategories = $service->getAllAvailableCategories();
            return View('site.partials.resumeupload', compact('user_resume', 'category', 'allAvailableCategories'));
        }
    }

    public function userProfileEdit(Requests\ProfileEditRequest $profileEditRequest) {
        $input = $profileEditRequest->all();
        $service = new ProfileAssetService();
        if ($input['ins_disability_adjustment'] != 1 || $input['ins_disability_adjustment'] == 0) {
            $input['ins_reasonableadjustmentrequired'] = null;
        }
        $category = $service->profileEdit($input);
        $service = new JobAssetService();
        $profile_det = $service->getProfileDetails();
        if(trim($input['personalemail']) != trim($input['old_personalemail'])){
            $emailService = new EmailRepository();
            $caseManager = null;
            if($profile_det instanceof Employee) {
                $caseManager = $profile_det->caseManager;
            }
            $message = \View::make('site/email/user_email_change_notification',array(
                'profile_det' => $profile_det , 'oldemail' => $input['old_personalemail'] , 'newemail' => $input['personalemail'],
                'case_manager' => $caseManager
            ))->render();
            $subject = 'Email address change notification for '  . $profile_det->new_firstname ;
            $from = Config::get('ins_emails.user_email_change.from');
            $to = $input['old_personalemail'];
            $emailService->send(    $message ,$subject , $from , $to );
            $to = $input['personalemail'];
            $emailService->send(    $message ,$subject , $from , $to );
        }
        return View::make('site.partials.profileview', array('profile_det' => $profile_det));
    }

    public function userDownloadResume($id) {
        $resumeRep = new ResumeRepository();
        $data = $resumeRep->downloadfile($id);
        if($data['status']){
            return $data['data'];
        } else {
            echo $data['data'];
            exit;
        }
    }

    public function userDownloadCvLetter($id) {
        $resumeRep = new ResumeRepository();
        return $resumeRep->downloadcvletter($id);
    }

    public function userDownloadsupportDoc($id) {
        $resumeRep = new ResumeRepository();
        return $resumeRep->downloadsupportpdf($id);
    }

    public function userPDFCapbs($id) {
        $resumeRep = new ResumeRepository();
        return $resumeRep->downloadpdf($id);
    }

    public function deleteUserCategory() {
        $input = Input::all();
        $service = new ProfileAssetService();
        $category = $service->deleteCategory($input['id']);
        $service = new JobAssetService();
        $category = $service->getCategory();
        $user_category = $service->getUserCategory();
        $user = DB::table('users')->where('users.id', '=', Auth::id())->first();
        return View('site.partials.jobcategory', compact('user', 'category', 'user_category'));
    }

    public function deleteUserLocation() {
        $input = Input::all();
        $service = new ProfileAssetService();
        $service->deleteUserLocation($input['id']);
        $userID = Auth::id();
        if(!empty($userID)){
            $loc = new UserPushRepository();
            $loc->pushEmployeesLocations($userID);
        }
        $service = new JobAssetService();
        $location = $service->getLocation();
        $user_location = $service->getUserLocation();
        return View('site.partials.location', compact('location','user_location'));
    }

    public function deleteUserResume() {
        $input = Input::all();
        $service = new ProfileAssetService();
        $service->deleteUserResume($input['id']);
        $service = new JobAssetService();
        $user_resume = $service->getUserResume();
        $category = $service->getCategory();
        // This variable is used resources/views/site/partials/resumeupload.blade.php to list all the available
        // categories when the user is uploading a Resume.
        $allAvailableCategories = $service->getAllAvailableCategories();
        return View('site.partials.resumeupload', compact('user_resume', 'category', 'allAvailableCategories'));
    }

    public function postCategory() {
        $input = Input::all();
        $service = new ProfileAssetService();
        $getcategorycount = $service->getCategoryCount();
        $userID = Auth::id();
        $count_cate = $getcategorycount->count;
        if(!isset($input['category']) || empty($input['category'])) {
            if(isset($input['popup-category-val']) || !empty($input['popup-category-val'])){
                $category = $service->postCategoryPending($input['category_id'], $input['add_category_txt']);
                $user = DB::table('users')->join('ins_employees','ins_employees.user_id', '=','users.id')->where('users.id','=',$userID)->first();
                $cat = DB::table('ins_job_category')->where('id','=',$input['category_id'])->first();
                $cacem_user = DB::table('ins_system_users')->where('systemuserid','=',$user->ownerid)->select('ins_system_users.*')->first();
                $message = "Role description/advert uploaded successfully";
                $emailService = new EmailRepository();
                $datail['name'] = $user->first_name . " " .$user->last_name;
                $datail['email'] = $user->email;
                $datail['contact'] = $user->new_personalmobilenumber;
                $datail['othercontact'] = $user->new_personalhomenumber;
                $datail['category'] = $cat->category_name;
                if(isset($cacem_user->fullname)){
                    $datail['cname'] = $cacem_user->fullname;
                } else {
                    $datail['cname'] = '';
                }
                $datail['msg'] = $input['add_category_txt'];
                $message = \View::make('site/email/category-added-notification',array('datail' => $datail ))->render();
                $subject = 'A category is added to the profile of ' . $user->first_name;
                $from = Config::get('ins_emails.user_category_notification.from');
                $to =  Config::get('ins_emails.user_category_notification.to');
                $emailService->send($message ,$subject, $from, $to);
                if(isset($cacem_user->internalemailaddress)){
                    $to = $cacem_user->internalemailaddress;
                    $emailService->send($message, $subject, $from, $to);
                }
                $service = new JobAssetService();
                $category = $service->getCategory();
                $user_category = $service->getUserCategory();
                $user = DB::table('users')->where('users.id', '=', Auth::id())->first();
                return View('site.partials.jobcategory', compact('user', 'category', 'user_category'));
            } else {
                return trans('messages.apply.category_profile');
            }
        } else if ($count_cate >= 8) {
            return trans('messages.apply.category_more');
        } else {
            $category = $service->postCategory($input['category']);
            if(!empty($userID)){
                $loc = new UserPushRepository();
                $loc->pushEmployeesCategories($userID);
            }
            $service = new JobAssetService();
            $category = $service->getCategory();
            $user_category = $service->getUserCategory();
            $user = DB::table('users')->where('users.id', '=', Auth::id())->first();
            return View('site.partials.jobcategory', compact('user', 'category', 'user_category'));
        }
    }

    public function postSkillAssesment() {
        $input = Input::all();
        $service = new ProfileAssetService();
        $category = $service->postSkillAssesment($input['checkbox']);
        return Redirect::to('site/profile')->with('message', trans('messages.apply.skillssuccess'));
    }

    public function uploadRD($job_id) {
        $job_id = Jobs::findByCrmJobid($job_id);
        if(!empty($job_id)){
            $service = new ProfileAssetService();
            $job_det = $service->getJobDet($job_id->id);
            if(!empty($job_det)){
                $suburbs = $service->getSuburbs();
                return View::make('site.home.uploadRD', array('job' => $job_det, 'suburbs' => $suburbs));
            } else {
                $message = "Job/Agency detail not found.";
                return View::make('site.home.error', array('message' => $message));
            }
        } else {
            $message = "Job expired. Can not upload Role description";
            return View::make('site.home.error', array('message' => $message));
        }
    }

    /**
     * This function send an email with the error encountered while parsing the RD.
     *
     * @param $errorMessage
     * @param $rdFile
     * @param $parsedDataReceived
     * @param null $rdSenderDetails
     *
     * @return mixed
     */
    protected function sendNotificationForFailedRDParsing($errorMessage, $rdFile, $parsedDataReceived, $rdSenderDetails = null) {
        $to = !empty(config('ins_emails.rd_failed_parsing')) ? config('ins_emails.rd_failed_parsing') : null;
        if(!empty($to)) {
            $detailedError = "";
            if (is_array($parsedDataReceived)) {
                $detailedError .= '<p><ul>';
                foreach ($parsedDataReceived as $key => $value) {
                    if (is_string($value)) {
                        $detailedError .= "<li>" . $key . " = " . $value;
                    } else {
                        $detailedError .= "<li>" . $key . " = " . print_r($value, true);
                    }
                }
                $detailedError .= '</ul></p>';
            }
            $emailView = view('site.email.RD_failed_parsing', ['detailedError' => $detailedError, 'errorMessage' => $errorMessage, 'rdSenderDetails' => $rdSenderDetails]);
            return app('App\Models\Services\EmailService')->send($emailView, 'Failed to parse RD', "Mobility@inscm.com.au", $to, ['attachments' => $rdFile]);
        } else {
            \App\Models\Services\AuditService::log('Error', 'Failed to send emails', __FILE__.'@'.__LINE__.' in class'.__CLASS__);
        }
    }

    /**
     * @param $job_id
     * @return mixed
     */
    public function postuploadPDF($job_id) {
        $formData = Input::all();
        $advertText = !empty($formData['advert']) ? $formData['advert'] : null;
        if (!isset($formData['inputfile']) || empty($formData['inputfile'])) {
            return Redirect::back()->with('error', trans('messages.apply.failed_pdf'))->withInput();
        } else if (!in_array(pathinfo($formData['inputfile']->getClientOriginalName(), PATHINFO_EXTENSION), array('pdf'))) {
            return Redirect::back()->with('error', trans('messages.apply.pdf_format'))->withInput();
        } else if ($formData['inputfile']->getSize() > 5000000) {
            return Redirect::back()->with('error', trans('messages.apply.resume_size'))->withInput();
        } else {
            $resumeRepository = new ResumeRepository();
            $resumeRepository->upoadRole_desc($formData['inputfile'], $job_id);
            $profileAssetService = app('App\Models\Services\ProfileAssetService');
            $fileClientOriginalName = $formData['inputfile']->getClientOriginalName();
            $fileNameWithPathInStorage = $formData['inputfile']->move(storage_path('download'), $fileClientOriginalName);
            $curlFile = curl_file_create($fileNameWithPathInStorage, 'application/pdf', $fileClientOriginalName);
            $curlFormData = ['file' => $curlFile];
            $ch = curl_init();
            $curlOptions = [
                CURLOPT_URL => Config::get('jojari.govjobsparser.url'),
                CURLOPT_RETURNTRANSFER => true,
                CURLINFO_HEADER_OUT => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $curlFormData
            ];
            curl_setopt_array($ch, $curlOptions);
            $curlResult = curl_exec($ch);
            $curlError = curl_error($ch);
            $jobDetails = Jobs::find($job_id);
            if($curlError){
                $this->sendNotificationForFailedRDParsing($curlError, $curlFile, $curlResult, $jobDetails);
                return Redirect::back()->with('error', trans('messages.rd_upload.failed_to_parse',[
                    'error_message' => $curlError
                ]))->withInput();
            } else {
                $roleDescription = json_decode($curlResult, true);
                if (empty($roleDescription['capabilities']) || (sizeof($roleDescription['capabilities']) != 16
                        && sizeof($roleDescription['capabilities']) != 20)) {
                    if(empty($roleDescription['capabilities'])) {
                        $errorDescription = 'No capabilities found.';
                    } else {
                        $errorDescription = "Only " . sizeof($roleDescription) . " capabilities found. 16 or 20 capabilities required.";
                    }
                    $this->sendNotificationForFailedRDParsing($errorDescription, $curlFile, $curlResult, $jobDetails);
                    return Redirect::back()->with('error', trans('messages.rd_upload.failed_to_parse',[
                        'error_message' => $errorDescription
                    ]))->withInput();
                }
                $roleDescriptionContent = !empty($roleDescription['description']) ? $roleDescription['description'] : "";
                $roleDescriptionTempFile = tmpfile();
                fwrite($roleDescriptionTempFile, $roleDescriptionContent);
                $fileProxy = new FileProxy(stream_get_meta_data($roleDescriptionTempFile)['uri'], 'file');
                $parsedResumeData = app('App\Models\Gateways\Redact\ResumeRedact')->clean($fileProxy);
                fclose($roleDescriptionTempFile);
                $jobRepository = app('App\Models\Repositories\JobRepository');
                $pushJobmatchStatus = app('App\Models\Services\PushJobmatchStatus');
                if(!empty($jobDetails)){
                    if(!empty($parsedResumeData) && !empty($parsedResumeData->skills)){
                        foreach($parsedResumeData->skills as $skill) {
                            $jobRepository->insertJobSkill($jobDetails->id, $skill);
                        }
                    }
                    if(!empty($roleDescription['capabilities'])){
                        $profileAssetService->postJobCapabilitySkills($roleDescription['capabilities'], $job_id);
                        $jobCapability = app('App\Models\Repositories\PushJobCapabilityRepository')->getJobCapability($job_id);
                        app('App\Models\Services\PushJobCapability')->pushJobApplied($jobCapability, $jobDetails->id);
                        $jobMatches = \DB::table('ins_jobmatch')->where("job_id", '=', $job_id)->get();
                        if (!empty($jobMatches)) {
                            foreach ($jobMatches as $jobMatch) {
                                $result = $pushJobmatchStatus->pushJobMatchStatus($jobMatch->new_jobmatchedid, 121660004);
                                if (isset($result['success'])) {
                                    \DB::table('ins_jobmatch')->where("job_id", '=', $job_id)->update(['new_matchstatus' => -1]);
                                } else {
                                    \DB::table('ins_jobmatch')->where("job_id", '=', $job_id)->update(['new_matchstatus' => 121660004]);
                                }
                            }
                        }
                        $pushJobmatchStatus->pushJobStatus($jobDetails->jobid, 'new_pdreceived', $status = 1, 'boolean');
                    }
                    if(!empty($roleDescription['primaryProps'])){
                        $profileAssetService->postJobAgencyDetails($roleDescription['primaryProps'], $job_id);
                    } else {
                        $this->sendNotificationForFailedRDParsing('No capabilities found in the parsed document', $curlFile, $roleDescription, $jobDetails);
                        return Redirect::back()->with('error', trans('messages.apply.jobcapab'));
                    }
                    if(!empty($roleDescription['description'])){
                        $profileAssetService->postRoleDescription($roleDescription['description'], $job_id);
                    } else {
                        $this->sendNotificationForFailedRDParsing('No description found in the parsed document', $curlFile, $roleDescription, $jobDetails);
                        return Redirect::back()->with('error', trans('messages.apply.jobDescription'));
                    }
                    if(!empty($formData['inputfileadvert'])){
                        $advertFile = $formData['inputfileadvert'];
                        $contentType = $advertFile->getClientMimeType();
                        $options = [
                            'content_type' => $contentType,
                            'write_file' => true,
                            'destination' => storage_path() . '/uploads',
                            'name' => $advertFile->getClientOriginalName()
                        ];
                        $advertFileProxy = new FileProxy($advertFile, 'desktop', $options);
                        $parsedAdvertData = app('App\Models\Gateways\Redact\ResumeRedact')->clean($advertFileProxy);

                        if(!empty($parsedAdvertData) && !empty($parsedAdvertData->skills)){
                            foreach($parsedAdvertData->skills as $skill){
                                $jobRepository->insertJobSkill($jobDetails->id, $skill);
                            }
                            if(!empty($parsedAdvertData->extra_info['text'])){
                                $advertText = str_replace("  ", "<br>", $parsedAdvertData->extra_info['text']);
                            }
                        }
                    }
                    if(!empty($advertText)){
                        $jobDetails->advert = htmlentities($advertText);
                    }
                    $fields = [
                        ['name' => 'ins_advertdetails', 'value' => $advertText, 'type' => 'string'],
                        ['name' => 'ins_advertreceived', 'value' => 1, 'type' => 'boolean'],
                    ];
                    if(!empty($formData['hiring_name'])){
                        $jobDetails->hiring_manager_name = $formData['hiring_name'];
                        $fields[] = ['name' => 'new_enquiriesname', 'value' => $jobDetails->hiring_manager_name, 'type' => 'string'];
                    }
                    if(!empty($formData['hiring_phone'])){
                        $jobDetails->hiring_manager_phone = $formData['hiring_phone'];
                        $fields[] = ['name' => 'new_enquiriesnumber', 'value' => $jobDetails->hiring_manager_phone, 'type' => 'string'];
                    }
                    if(!empty($formData['hiring_email'])){
                        $jobDetails->hiring_manager_email = $formData['hiring_email'];
                        $fields[] = ['name' => 'ins_enquiriesemail', 'value' => $jobDetails->hiring_manager_email, 'type' => 'string'];
                    }
                    if(!empty($formData['workplace'])){
                        $jobDetails->workplace_location = $formData['workplace'];
                        $fields[] = ['name' => 'new_suburbid', 'value' => $jobDetails->workplace_location, 'type' => 'entity', 'entity_name' => 'new_suburb'];
                    }
                    if(!empty($formData['length_term'])){
                        $jobDetails->length_term = $formData['length_term'];
                        $fields[] = ['name' => 'ins_lengthterm', 'value' => $jobDetails->length_term, 'type' => 'option'];
                    }
                    if(!empty($formData['length_term_other'])){
                        $jobDetails->length_term_other = $formData['length_term_other'];
                    }

                    $jobDetails->save();

                    if($jobDetails->length_term == 121660006){
                        $fields[] = ['name' => 'ins_otherlengthterm', 'value' => $jobDetails->length_term_other, 'type' => 'string'];
                    }
                    $pushJobmatchStatus->pushJobStatuses($jobDetails->jobid, $fields);
                    $message = "Role description/advert uploaded successfully";
                    $emailService = new EmailRepository();
                    $message_email = View::make('site/email/role_description_upload', 
                        array('details' => $jobDetails, 'advert' => $advertText)
                    );
                    $subject = 'RD received for ' . $jobDetails->job_title . ' (req no. ' . $jobDetails->vacancy_reference_id . ')';
                    $to = Config::get('ins_emails.rd_email_cc.to');
                    $from = Config::get('ins_emails.rd_email_cc.from');
                    $emailService->sendAttachment($to, $from, $subject, '', $message_email, [$fileNameWithPathInStorage]);
                    return View::make('site.home.success', array('message' => $message,));
                }
            }
        }
    }

    public function managerUserCapabilityUpload() {
        $input = Input::all();
        $userCapa = new ResumeRepository();
        $arr_returns = array('message' => trans('messages.apply.failed_pdf'), 'type' => 'alert-danger');
        if(!empty($input)){
            $userID = $input['event_value'];
            $fileupload = $input['fileupload'][$input['event_value']];
            if(!empty($fileupload)){
                if(!in_array(pathinfo($fileupload->getClientOriginalName(), PATHINFO_EXTENSION), array('pdf'))) {
                    $arr_returns = array('message' => trans('messages.apply.pdf_format'), 'type' => 'alert-danger');
                } elseif($fileupload->getSize() > 5000000) {
                    $arr_returns = array('message' => trans('messages.apply.resume_size'), 'type' => 'alert-danger');
                } else {
                    $upload_user_cap = $userCapa->uploadUserCapab($fileupload, $userID);
                    $service = new ProfileAssetService();
                    $filename = $fileupload->getRealPath();
                    $postname = $fileupload->getClientOriginalName();
                    $name = curl_file_create($filename, 'application/pdf', $postname);
                    $data = array('file' => $name);

                    $ch = curl_init();
                    $options = array(
                        CURLOPT_URL => Config::get('jojari.govjobsparser.url'),
                        CURLOPT_RETURNTRANSFER => true,
                        CURLINFO_HEADER_OUT => true,
                        CURLOPT_HEADER => false,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_POST => true,
                        CURLOPT_POSTFIELDS => $data
                    );
                    curl_setopt_array($ch, $options);
                    $result = curl_exec($ch);
                    $err = curl_error($ch);
                    if($err){
                        $arr_returns = array('message' => 'CURL Error #: '.$err, 'type' => 'alert-danger');
                    } else {
                        $role_description = json_decode($result, true);
                        if(!empty($role_description['capabilities'])){
                            $service->postUserCapability($role_description['capabilities'], $userID);
                            $this->UserCapabilityRepository = new PushUserCapabilityRepository();
                            $crm_user_id = $service->findusercrmid($userID);
                            $userCapability = $this->UserCapabilityRepository->getUserCapability();
                            $user_capabs = new PushUserCapability();
                            $insLocations = $user_capabs->pushUserCapaba($userCapability,$crm_user_id->crm_user_id);
                            $arr_returns = array('message' => trans('messages.apply.usecapabs'), 'type' => 'alert-success');
                        } else {
                            $arr_returns = array('message' => trans('messages.apply.usecapabserror'),'type' => 'alert-danger');
                        }
                    }
                }
            }
        }
        return View::make('admin.partials.iframe.iframe-upload', $arr_returns)->render();
    }

    public function userCapabilityUpload() {
        $userID = Auth::id();
        $input = Input::all();
        $userCapa = new ResumeRepository();
         if (!isset($input['inputfile']) || empty($input['inputfile'])) {
            return Redirect::back()->with('error', trans('messages.apply.failed_pdf'));
        }else if (! in_array(pathinfo($input['inputfile']->getClientOriginalName(), PATHINFO_EXTENSION), array('pdf'))) {
            return Redirect::back()->with('error', trans('messages.apply.pdf_format'));
        } else if ($input['inputfile']->getSize() > 5000000) {
            return Redirect::back()->with('error', trans('messages.apply.resume_size'));
        } else {
            $upload_user_cap = $userCapa->uploadUserCapab($input['inputfile'],$userID);
            $service = new ProfileAssetService();
            $filename = $input['inputfile']->getRealPath();
            $postname = $input['inputfile']->getClientOriginalName();
            $name = curl_file_create($filename, 'application/pdf', $postname);
            $data = array('file' => $name);
            $curl = curl_init();

            $ch = curl_init();
            $options = array(CURLOPT_URL => Config::get('jojari.govjobsparser.url'),
                CURLOPT_RETURNTRANSFER => true,
                CURLINFO_HEADER_OUT => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $data
            );
            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            $err = curl_error($ch);
            if($err){
                echo "cURL Error #:" . $err;
            } else {
                $role_description = json_decode($result, true);
                if(!empty($role_description['capabilities'])){
                    $service->postUserCapability($role_description['capabilities'],$userID);
                    $this->UserCapabilityRepository = new PushUserCapabilityRepository();
                    $crm_user_id =  $service->findusercrmid($userID);
                    $userCapability = $this->UserCapabilityRepository->getUserCapability();
                    $user_capabs = new PushUserCapability();
                    $insLocations = $user_capabs->pushUserCapaba($userCapability,$crm_user_id->crm_user_id);
                    return Redirect::to('site/profile')->with('message', trans('messages.apply.usecapabs'));
                }
                return Redirect::to('site/profile')->with('error', trans('messages.apply.usecapabserror'));
            }
        }
    }

    public function uploadUserCapabilities($crm_user_id) {
        $user = \DB::table('users')->where("crm_user_id",'=',$crm_user_id)->first();
        if(!empty($user)){
            return View::make('site.home.upload-user-capabilities', array('user' =>$user, ));
        } else {
            $message = "User not found in system";
            return View::make('site.home.errorrdupload', array('message' =>$message, ));
        }
    }

    public function postUploadUserCapabilities($crm_user_id) {
        $user_data = \DB::table('users')->where("crm_user_id",'=',$crm_user_id)->first();
        $userID = $user_data->id;
        $input = Input::all();
        $userCapa = new ResumeRepository();
        if(!isset($input['inputfile']) || empty($input['inputfile'])) {
            return Redirect::back()->with('error', trans('messages.apply.failed_pdf'));
        } else if (! in_array(pathinfo($input['inputfile']->getClientOriginalName(), PATHINFO_EXTENSION), array('pdf'))) {
            return Redirect::back()->with('error', trans('messages.apply.pdf_format'));
        } else if ($input['inputfile']->getSize() > 5000000) {
            return Redirect::back()->with('error', trans('messages.apply.resume_size'));
        } else {
            $upload_user_cap = $userCapa->uploadUserCapab($input['inputfile'],$userID);
            $service = new ProfileAssetService();
            $filename = $input['inputfile']->getRealPath();
            $postname = $input['inputfile']->getClientOriginalName();
            $name = curl_file_create($filename, 'application/pdf', $postname);
            $data = array('file' => $name);
            $curl = curl_init();
            $ch = curl_init();
            $options = array(CURLOPT_URL => Config::get('jojari.govjobsparser.url'),
                CURLOPT_RETURNTRANSFER => true,
                CURLINFO_HEADER_OUT => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $data
            );
            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            $err = curl_error($ch);
            if($err){
                if(!empty($input['inputfile']) && !empty($postname)) {
                    $fileNameWithPathInStorage = $input['inputfile']->move(storage_path('download'), $postname);
                    $curlFile = curl_file_create($fileNameWithPathInStorage, 'application/pdf', $postname);
                    $this->sendNotificationForFailedRDParsing($err, $curlFile, $result, Employee::where(['user_id' => $user_data->id])->first());
                }
                return Redirect::back()->with('error', trans('messages.rd_upload.failed_to_parse',['error_message' => $err]))->withInput();
            } else {
                $role_description = json_decode($result, true);
                if(empty($role_description['capabilities']) || (sizeof($role_description['capabilities']) != 16
                        && sizeof($role_description['capabilities']) != 20)) {
                    if(empty($role_description['capabilities'])){
                        $errorDescription = 'No capabilities found.';
                    } else {
                        $errorDescription = "Only " . sizeof($role_description) . " capabilities found. 16 or 20 capabilities required.";
                    }
                    $fileNameWithPathInStorage = $input['inputfile']->move(storage_path('download'), $postname);
                    $curlFile = curl_file_create($fileNameWithPathInStorage, 'application/pdf', $postname);
                    $this->sendNotificationForFailedRDParsing($errorDescription, $curlFile, $result, Employee::where(['user_id' => $user_data->id])->first());
                    return Redirect::back()->with('error', trans('messages.rd_upload.failed_to_parse',[
                        'error_message' => $errorDescription
                    ]))->withInput();
                }
                if(!empty($role_description['capabilities'])){
                    $service->postUserCapability($role_description['capabilities'],$userID);
                    $this->UserCapabilityRepository = new PushUserCapabilityRepository();
                    $userCapability = $this->UserCapabilityRepository->getUserCapability($userID);
                    $user_capabs = new PushUserCapability();
                    $insLocations = $user_capabs->pushUserCapaba($userCapability,$crm_user_id);
                    $message = trans('messages.apply.usecapabs');
                    return View::make('site.home.errorrdupload', array('message' => $message));
                }
                $message = trans('messages.apply.usecapabserror');
                if(!empty($input['inputfile']) && !empty($postname)) {
                    $fileNameWithPathInStorage = $input['inputfile']->move(storage_path('download'), $postname);
                    $curlFile = curl_file_create($fileNameWithPathInStorage, 'application/pdf', $postname);
                    $this->sendNotificationForFailedRDParsing($message, $curlFile, $result, Employee::where(['user_id' => $user_data->id])->first());
                }
                return View::make('site.home.errorrdupload', array('message' =>$message));
            }
        }
    }

    public function getuserCapabilities() {
        $cap_match_repo = new CapabilityMatchRepository();
        $u = new \stdClass;
        $u->candidate_id  = Auth::id();
        $user_capabilities = $cap_match_repo->getCandidateCapabilitiesCriteria($u);
        return View('site.partials.user-capabilities', array(
            'user_capabilities' => $user_capabilities
        ));
    }

    public function addSkillAssessment() {
        $data = Input::all();
        if (empty($data['skill_id']) || empty($data['recency_id']) || empty($data['frequency_id']) || empty($data['level_id'])) {
            return '';
        }
        $service = new ProfileAssetService();
        $id = $service->addSkillAssessment($data);
        $service = new JobAssetService();
        $skills = $service->getSkillAssessments();
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
        $service = app()->make('App\Models\Repositories\UserSkillPushRepository');
        $service->createEmployeesSkill($id);
        return View('site.partials.skill-assessment', compact(
            'skills', 'frequency', 'recency', 'level'
        ));
    }

    public function updateSkillAssessment($id) {
        $service = new ProfileAssetService();
        $skill = $service->getSkillAssessment($id);
        if(!$skill){
            return '';
        }
        $data = Input::all();
        $skill->recency_id  = $data['recency_id'];
        $skill->frequency_id  = $data['frequency_id'];
        $skill->level_id  = $data['level_id'];
        $skill->comment = ! empty($data['comment']) ? substr($data['comment'], 0, 2000) : '';
        $skill->save();
        $service = app()->make('App\Models\Repositories\UserSkillPushRepository');
        $service->updateEmployeesSkill($id);
    }

    public function deleteSkillAssessment($id) {
        $service = new ProfileAssetService();
        $skill = $service->getSkillAssessment($id);
        if (!$skill) {
            return '';
        }
        $skill->active = 0;
        $skill->save();
        $service = app()->make('App\Models\Repositories\UserSkillPushRepository');
        $service->deleteEmployeesSkill($id);
    }

    public function suggestSkill() {
        $emailService = new EmailRepository();
        $userID = Auth::id();
        $user = DB::table('users')
            ->join('ins_employees', 'ins_employees.user_id', '=', 'users.id')
            ->where('users.id', '=', $userID)
            ->first();
        $data = Input::all();
        $message = View::make('site/email/suggest-skill', compact('user', 'data'))->render();
        $from = Config::get('ins_emails.user_category_notification.from');
        $to = Config::get('ins_emails.user_category_notification.to');
        $emailService->send($message, 'New Skill Suggestion', $from, $to);
    }
}
