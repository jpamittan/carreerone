<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', array(
  'as' => 'site-login-home',
  'uses' => 'site\AuthController@index'
));
Route::get('/register/activate/{token}', array(
  'as' => 'user-activate',
  'uses' => 'site\RegistrationController@confirm'
));
Route::post('/register/activate', array(
  'as' => 'user-activate-post',
  'uses' => 'site\RegistrationController@activate'
));
Route::group(['prefix' => 'admin'], function () {
  Route::get('/', 'Admin\AuthController@index');
  Route::get('login', 'Admin\AuthController@index');
  Route::post('/signin', array(
    'as' => 'admin-signin',
    'uses' => 'Admin\AuthController@login'
  ));
  Route::get('/logout', array(
    'as' => 'admin-logout',
    'uses' => 'Admin\AuthController@logout'
  ));
  Route::get('/home', array(
    'middleware' => 'auth',
    'as' => 'admin-home',
    'uses' => 'Admin\AdminController@home'
  ));
  Route::get('/users', array(
    'middleware' => 'auth',
    'as' => 'admin-users',
    'uses' => 'Admin\AdminController@userDetails'
  ));
  Route::get('/users/isactive', array(
    'middleware' => 'auth',
    'as' => 'admin-users-active',
    'uses' => 'Admin\AdminController@isActive'
  ));
  Route::get('/userform', array(
    'middleware' => 'auth',
    'as' => 'admin-users',
    'uses' => 'Admin\AdminController@userForm'
  ));
  Route::get('/userform/{id}', array(
    'middleware' => 'auth',
    'as' => 'admin-users',
    'uses' => 'Admin\AdminController@userForm'
  ));
  Route::get('/permissiondetails', array(
    'middleware' => 'auth',
    'as' => 'admin-users',
    'uses' => 'Admin\AdminController@permissionDetails'
  ));
  Route::get('/permissions', array(
    'middleware' => 'auth',
    'as' => 'admin-users',
    'uses' => 'Admin\AdminController@permissionForm'
  ));
  Route::post('/create', array(
    'middleware' => 'auth',
    'as' => 'admin-create-form',
    'uses' => 'Admin\AuthController@create'
  ));
  Route::get('/getpermissions', array(
    'middleware' => 'auth',
    'as' => 'admin-role-permissions',
    'uses' => 'Admin\AuthController@getPermissions'
  ));
  Route::post('/postpermissions', array(
    'middleware' => 'auth',
    'as' => 'admin-post-permissions',
    'uses' => 'Admin\AuthController@postPermissions',
  ));
});
Route::group(['prefix' => 'site'], function () {
  Route::get('/login', array(
    'as' => 'site-login',
    'uses' => 'site\AuthController@index'
  ));
  Route::get('/resources', [
    'as' => 'site-resources',
    'uses' => 'site\StaticPageController@getResources'
  ]);
  Route::get('/forgot-password', array(
    'as'  => 'forgot-password',
    'uses'  => 'site\AuthController@forgotPassword'
  ));
  Route::post('/forgot-password', array(
    'as'  => 'forgot-password',
    'uses'  => 'site\AuthController@forgotPassword'
  ));
  Route::get('/reset-password/{token}', array(
    'as'  => 'reset-password',
    'uses'  => 'site\AuthController@resetPassword'
  ));
  Route::post('/reset-password/{token}', array(
    'as'  => 'reset-password',
    'uses'  => 'site\AuthController@resetPassword'
  ));
  Route::post('/signin', array(
    'as' => 'site-signin',
    'uses' => 'site\AuthController@login'
  ));
  Route::post('/fileupload/{job_id}', array(
    'middleware' => 'auth',
    'as' => 'site-post-file',
    'uses' => 'site\AdminController@fileUpload'
  ));
  Route::get('/getJobs/{job_id}', array(
    'middleware' => 'auth',
    'as' => 'site-get-jobs',
    'uses' => 'site\JobController@getJob'
  ));
  Route::get('/jobview/{job_id}', array(
    'middleware' => 'auth',
    'as' => 'site-jobview',
    'uses' => 'site\JobController@jobView'
  ));
  Route::get('/jobapply/{job_id}', array(
    'middleware' => 'auth',
    'as' => 'site-job-apply',
    'uses' => 'site\JobController@jobApply'
  ));

  Route::get('/jobeoi/{job_id}', array(
    'middleware' => 'auth',
    'as' => 'site-job-apply-eoi',
    'uses' => 'site\JobController@jobApplyEoi'
  ));

  Route::post('/jobeoirejected', array(
    'middleware' => 'auth',
    'as' => 'site-job-apply-eoi-rejected',
    'uses' => 'site\JobController@jobRejectedEoi'
  ));
  Route::post('/jobreject/{job_id}', array(
    'middleware' => 'auth',
    'as' => 'site-job-reject',
    'uses' => 'site\JobController@jobReject',
  ));
  Route::get('/dashboard', array(
    'middleware' => 'auth',
    'as' => 'site.home.dashboard',
    'uses' => 'site\AdminController@dashboard',
  ));
  Route::get('/dashboard/jobs', array(
    'middleware' => 'auth',
    'as' => 'site.home.dashboard.page',
    'uses' => 'site\AdminController@dashboardPage',
  ));
  Route::get('/dashboard/applyhistorypage', array(
    'middleware' => 'auth',
    'as' => 'site.home.applyhistory.page',
    'uses' => 'site\AdminController@applyHistoryPage',
  ));
  Route::get('/logoutsite', array(
    'as' => 'site-logout',
    'uses' => 'site\AuthController@logout'
  ));
  Route::get('/nojobs', array(
    'middleware' => 'auth',
    'as' => 'site-nojob',
    'uses' => 'site\JobController@noJobs'
  ));
  Route::get('/profile', array(
    'middleware' => 'auth',
    'as' => 'site.home.profile',
    'uses' => 'site\AdminController@profile'
  ));
  Route::get('/calandar_view', array(
    'middleware' => 'auth',
    'as' => 'site.home.calandar',
    'uses' => 'site\AdminController@calandar'
  ));
  Route::get('/settings', array(
    'middleware' => 'auth',
    'as' => 'site.home.settings',
    'uses' => 'site\AdminController@settings'
  ));
  Route::post('/uploadcsv', array(
    'middleware' => 'auth',
    'as' => 'site.upload.csv',
    'uses' => 'site\ReadCSVController@importCSV'
  ));
  Route::get('/calandar_dates', array(
    'middleware' => 'auth',
    'as' => 'site.home.calanddates',
    'uses' => 'site\JobController@candidateInterviewDates'
  ));
  Route::get('/calandar_pending_dates', array(
    'middleware' => 'auth',
    'as' => 'site.home.calandpenddates',
    'uses' => 'site\JobController@candidatePendingDates'
  ));
  Route::get('/calandar_pending_dates_candidate/{id}', array(
    'middleware' => 'auth',
    'as' => 'site.home.calandpenddatescandidate',
    'uses' => 'site\JobController@candidateCalandarPendingDates'
  ));
  Route::post('/delete', array(
    'middleware' => 'auth',
    'as' => 'site.job.delete',
    'uses' => 'site\JobController@deleteJob'
  ));
  Route::get('/interview', array(
    'middleware' => 'auth',
    'as' => 'confirm-interview',
    'uses' => 'site\AdminController@confirmInterview'
  ));
  Route::get('/scheduled_interview_details/{job_id}', array(
    'middleware' => 'auth',
    'as' => 'scheduled-details',
    'uses' => 'site\AdminController@ScheduledDetails'
  ));
  Route::get('/completed_interview_details/{job_id}', array(
    'middleware' => 'auth',
    'as' => 'completed-details',
    'uses' => 'site\AdminController@CompletedInterviewDetails'
  ));
  Route::post('/accept_interview', array(
    'middleware' => 'auth',
    'as' => 'accept-interview',
    'uses' => 'site\JobController@acceptInterview'
  ));
  Route::post('/reject_interview', array(
    'middleware' => 'auth',
    'as' => 'accept-interview',
    'uses' => 'site\JobController@rejectInterview'
  ));
  Route::get('/pending_confirmation/{id}', array(
    'middleware' => 'auth',
    'as' => 'confirm-pending-interview',
    'uses' => 'site\JobController@pendingInterviewConfirm'
  ));
  Route::get('/location_keywords/{keywords}', array(
    'middleware' => 'auth',
    'as' => 'location-keyword',
    'uses' => 'site\ProfileController@location'
  ));
  Route::get('/category/{keywords}', array(
    'middleware' => 'auth',
    'as' => 'category-keyword',
    'uses' => 'site\ProfileController@category'
  ));
  Route::post('/post_location', array(
    'middleware' => 'auth',
    'as' => 'location-post',
    'uses' => 'site\ProfileController@postLocation'
  ));
  Route::post('/user_resume', array(
    'middleware' => 'auth',
    'as' => 'profile-file-upload',
    'uses' => 'site\ProfileController@userResumeUpload'
  ));
  Route::post('/profile_edit', array(
    'middleware' => 'auth',
    'as' => 'site-profile-edit',
    'uses' => 'site\ProfileController@userProfileEdit'
  ));
  Route::get('/download_resume/{id}', array(
    'middleware' => 'auth',
    'as' => 'site-download-file',
    'uses' => 'site\ProfileController@userDownloadResume'
  ));
  Route::get('/download_rd/{id}', array(
    'middleware' => 'auth',
    'as' => 'site-download-rd-file',
    'uses' => 'site\JobController@downloadRD'
  ));
  Route::get('/download_cvletter/{id}', array(
    'middleware' => 'auth',
    'as' => 'site-download-file',
    'uses' => 'site\ProfileController@userDownloadCvLetter'
  ));
  Route::get('/download_suppdf/{id}', array(
    'middleware' => 'auth',
    'as' => 'site-download-file',
    'uses' => 'site\ProfileController@userDownloadsupportDoc'
  ));
  Route::get('/download_pdf/{id}', array(
    'middleware' => 'auth',
    'as' => 'site-download-PDF',
    'uses' => 'site\ProfileController@userPDFCapbs'
  ));
  Route::post('/delete_user_category', array(
    'middleware' => 'auth',
    'as' => 'delete-user-category',
    'uses' => 'site\ProfileController@deleteUserCategory'
  ));
  Route::post('/post_category', array(
    'middleware' => 'auth',
    'as' => 'site-post-category',
    'uses' => 'site\ProfileController@postCategory'
  ));
  Route::post('/post_skilasses', array(
    'middleware' => 'auth',
    'as' => 'site-post-skillassesment',
    'uses' => 'site\ProfileController@postSkillAssesment'
  ));
  Route::post('/suggest-skill', array(
    'middleware' => 'auth',
    'as' => 'site-suggest-skill',
    'uses' => 'site\ProfileController@suggestSkill'
  ));
  Route::post('/add-skill-assessment', array(
    'middleware' => 'auth',
    'as' => 'site-add-skill-assessment',
    'uses' => 'site\ProfileController@addSkillAssessment'
  ));
  Route::put('/update-skill-assessment/{id}', array(
    'middleware' => 'auth',
    'as' => 'site-update-skill-assessment',
    'uses' => 'site\ProfileController@updateSkillAssessment'
    ));
  Route::delete('/delete-skill-assessment/{id}', array(
    'middleware' => 'auth',
    'as' => 'site-delete-skill-assessment',
    'uses' => 'site\ProfileController@deleteSkillAssessment'
  ));
  Route::post('/delete_user_location', array(
    'middleware' => 'auth',
    'as' => 'delete-user-category',
    'uses' => 'site\ProfileController@deleteUserLocation'
  ));
  Route::get('/role_description/{job_id}', array(
    'as' => 'recruiter-pdf-upoad',
    'uses' => 'site\ProfileController@uploadRD'
  ));
  Route::post('/upload_role_description/{job_id}', array(
    'as' => 'recruiter-upload-roledesc',
    'uses' => 'site\ProfileController@postuploadPDF'
  ));
  Route::get('/user_capabilities/{crm_user_id}', array(
    'as' => 'upload-user-capabilities',
    'uses' => 'site\ProfileController@uploadUserCapabilities'
    ));
  Route::post('/upload_user_capabilities/{crm_user_id}', array(
    'as' => 'post-upload-user-capabilities',
    'uses' => 'site\ProfileController@postUploadUserCapabilities'
  ));
  Route::get('/pdf_success', array(
    'as' => 'pdf-success',
    'uses' => 'site\ProfileController@suceessPDF'
  ));
  Route::post('/delete_user_resume', array(
    'as' => 'Delete-user-resume',
    'uses' => 'site\ProfileController@deleteUserResume'
  ));
  Route::post('/user_capability', array(
    'middleware' => 'auth',
    'as' => 'site-user-capabilities',
    'uses' => 'site\ProfileController@userCapabilityUpload'
  ));
  Route::post('/manager_user_capability', array(
    'middleware' => 'auth',
    'as' => 'site-manager-capability',
    'uses' => 'site\ProfileController@managerUserCapabilityUpload'
  ));
  Route::get('/schedule_interview/{id}', array(
    'as' => 'site-interview-schedule',
    'uses' => 'site\ScheduleInterviewController@getCandiatesInterview'
  ));
  Route::get('/selected_dates/{id}', array(
    'as' => 'site-seleted-dates',
    'uses' => 'site\ScheduleInterviewController@getCandidateSeletedDates'
  ));
  Route::get('/post_schedule_interview', array(
    'as' => 'site-post-interview-schedule',
    'uses' => 'site\ScheduleInterviewController@postCandiatesInterview'
  ));
  Route::get('/screened_success', array(
    'as' => 'site-screened-success',
    'uses' => 'site\ScheduleInterviewController@screenedSucess'
  ));
  Route::get('/scheduled_success', array(
    'as' => 'site-schedule-success',
    'uses' => 'site\ScheduleInterviewController@scheduledSucess'
  ));
   Route::get('/recruiteremail', array(
    'as' => 'site-recruiter-Email',
    'uses' => 'site\ScheduleInterviewController@recruiterEmailView'
  ));
  Route::get('/candidate_details/{user_id}/{job_id}', array(
    'as' => 'site-case-manager-Email',
    'uses' => 'site\CaseManagerController@getCandidateDetails'
  ));
  Route::get('/candidate_detail_info/{user_id}/{job_id}', array(
    'as' => 'site-case-manager-info',
    'uses' => 'site\CaseManagerController@getCandidateInfo'
  ));
  Route::get('/candidate_detail_matching/{user_id}/{job_id}', array(
    'as' => 'site-case-manager-info',
    'uses' => 'site\CaseManagerController@getThreeLevelMatching'
  ));
  Route::get('/candidate_detail_profile/{user_id}/{job_id}', array(
    'as' => 'site-case-manager-info',
    'uses' => 'site\CaseManagerController@getCandidateProfile'
  ));
  Route::group(['middleware' => \App\Http\Middleware\RestrictedIPRouteMiddleware::class], function () {
    Route::get('/get-user-capabilities/{user_id}', array(
      'as' => 'site-get-user-capabilities',
      'uses' => 'site\ProfileController@getUserCapabilities'
    ));
  });
  Route::get('/candidate_detail', array(
    'as' => 'site-case-manager-Email',
    'uses' => 'site\CaseManagerController@getCandidateMatchDetails'
  ));
  Route::get('/candidate_detail_matching1', array(
    'as' => 'site-case-manager-info',
    'uses' => 'site\CaseManagerController@getThreeLevelMatching1'
  ));
  Route::get('/feedback/{crm_user_id}/{id}', array(
    'as'  => 'tell-us-how-you-went',
    'uses'  => 'site\EmailCandidateController@interviewFeedback'
  ));
  Route::post('/feedback/send-feedback', array(
    'as'  => 'send-feedback',
    'uses'  => 'site\EmailCandidateController@sendFeedback'
  ));
  Route::get('/careeronejobs', array(
    'middleware' => 'auth',
    'as' => 'site.home.careeronejobs',
    'uses' => 'site\SearchController@careeronejobs',
  ));
  Route::get('/search/getAreas', array(
    'as' => 'site.home.getAreas',
    'uses' => 'site\SearchController@getAreas',
  ));
  Route::get('/search/location/autocomplete', array(
    'as' => 'site.home.autocomplete',
    'uses' => 'site\SearchController@autocomplete',
  ));
});