<?php

namespace App\Models\Repositories;

use App\Models\Entities\Client;
use App\Models\Entities\User;
use App\Models\Services\EmailService;
use App\Models\Entities\Employee;
use App\Models\Entities\JobCategoryType;
use App\Models\Entities\InsLocation;
use App\Models\Entities\AgencyDetails;
use App\Models\Entities\CandidateCapability;
use DB, DateTime, DateTimeZone;

class UserImportRepository extends RepositoryBase {
    private $emailService;

    function __construct(EmailService $emailService) {
        $this->emailService = $emailService;
    }

    public function updateEmployeesCategory($employees) {
        foreach ($employees as $ins_employee) {
            $user = DB::table('users')->where('crm_user_id', '=', $ins_employee['new_employeeid'])->first();
            if (!empty($user)) {
                $this->updateCategories($user->id, $ins_employee);
            }
        }
    }

    /**
     * This function imports all the EIT from AVA's CRM into our database.
     * The script only imports employee records which have a valid email and ins_activeinava status is "true".
     *
     * @param $employees
     */
    public function importEmployees($employees) {
        $ctr_ActiveInAva = 0;
        $ctr_InActiveInAva = 0;
        $ctr_InsertedEmployee = 0;
        $ctr_UpdatedEmployee = 0;
        $ctr_ResetAccountEmployee = 0;
        $ctr_ParsedEmployee = 0;
        $total_crm_employees = count($employees);
        echo "Parsing now per employee...\n";
        $insEmployeesMissingFlagTobeActivated = [];
        //Print crm employee logs if total count is 1 for checking purposes if empty.
        if($total_crm_employees <= 1){
            print_r($employees);
        }
        foreach($employees as $ins_employee){
            $ctr_ParsedEmployee++;
            // Lets store the status in a variable so we can dump onto the screen as a result of the import.
            $activeInAVAStatus = isset($ins_employee['ins_activeinava']) ? $ins_employee['ins_activeinava'] : false;
            // Lets check if it passes the ins_activeinava flag.
            if ($activeInAVAStatus === true || $activeInAVAStatus === "true" || $activeInAVAStatus === 1) {
                $ctr_ActiveInAva++;
                try {
                    if (array_get($ins_employee, 'new_personalemail', null) === null) continue;
                    $employee = Employee::firstOrNew(['employeeid' => $ins_employee['new_employeeid']]);
                    $employee_old = DB::table('ins_employees')->where('employeeid', '=', $ins_employee['new_employeeid'])->first();
                    $createdon = array_get($ins_employee, 'createdon');
                    if (!empty($createdon)) {
                        $dt = new DateTime($createdon, new DateTimeZone('UTC'));
                        $dt->setTimezone(new DateTimeZone('Australia/Sydney'));
                        $createdon = $dt->format('Y-m-d');
                    }
                    $modifiedon = array_get($ins_employee, 'modifiedon');
                    if (!empty($modifiedon)) {
                        $dt = new DateTime($createdon, new DateTimeZone('UTC'));
                        $dt->setTimezone(new DateTimeZone('Australia/Sydney'));
                        $modifiedon = $dt->format('Y-m-d');
                    }
                    $employee->createdby = array_get($ins_employee, 'createdby.bId', null);
                    $employee->created_at = $createdon;
                    $employee->ins_jobpreference1 = array_get($ins_employee, 'ins_jobpreference1.bId', null);
                    $employee->ins_jobpreference2 = array_get($ins_employee, 'ins_jobpreference2.bId', null);
                    $employee->ins_jobpreference3 = array_get($ins_employee, 'ins_jobpreference3.bId', null);
                    $employee->ins_linkedinurl = array_get($ins_employee, 'ins_linkedinurl', null);
                    $employee->ins_skypeid = array_get($ins_employee, 'ins_skypeid', null);
                    $employee->modifiedby = array_get($ins_employee, 'modifiedby.bId', null);
                    $employee->updated_at = $modifiedon;
                    $employee->new_activejobseekingexternal = array_get($ins_employee, 'new_activejobseekingexternal') == "false" ? 'N' : 'Y';
                    $employee->new_age = array_get($ins_employee, 'new_age', null);
                    $employee->new_atsi = array_get($ins_employee, 'new_atsi') == "false" ? 0 : 1;
                    $employee->new_careerplancompleted = array_get($ins_employee, 'new_careerplancompleted') == "false" ? 'N' : 'Y';
                    $employee->new_completedprogram = array_get($ins_employee, 'new_completedprogram', null);
                    $employee->new_dateofbirth = array_get($ins_employee, 'new_dateofbirth', null);
                    $employee->new_daysundermanagement = array_get($ins_employee, 'new_daysundermanagement', null);
                    $employee->new_daysuntilfrexit = array_get($ins_employee, 'new_daysuntilfrexit', null);
                    $employee->new_decisiondate = array_get($ins_employee, 'new_decisiondate', null);
                    $employee->new_defaultjobmatchingjobcategorytype = $this->getDefaultCategoryType($ins_employee);
                    $employee->new_eitid = array_get($ins_employee, 'new_eitid', null);
                    $employee->new_emergencyaddressline1 = array_get($ins_employee, 'new_emergencyaddressline1', null);
                    $employee->new_emergencyaddressline2 = array_get($ins_employee, 'new_emergencyaddressline2', null);
                    $employee->new_emergencycontactname = array_get($ins_employee, 'new_emergencycontactname', null);
                    $employee->new_emergencycontactnumber = array_get($ins_employee, 'new_emergencycontactnumber', null);
                    $employee->new_emergencyemail = array_get($ins_employee, 'new_emergencyemail', null);
                    $employee->new_emergencymobilenumber = array_get($ins_employee, 'new_emergencymobilenumber', null);
                    $employee->new_emergencypostcode = array_get($ins_employee, 'new_emergencypostcode', null);
                    $employee->new_emergencyrelationship = array_get($ins_employee, 'new_emergencyrelationship', null);
                    $employee->new_emergencystate = array_get($ins_employee, 'new_emergencystate.bValue', null);
                    $employee->new_emergencysuburbid = array_get($ins_employee, 'new_emergencysuburbid.bId', null);
                    $employee->new_employeenumber = array_get($ins_employee, 'new_employeenumber', null);
                    $employee->new_employeestatus = array_get($ins_employee, 'new_employeestatus.bValue', null);
                    $employee->new_employmentrestrictions = array_get($ins_employee, 'new_employmentrestrictions', null);
                    $employee->new_excessdate = array_get($ins_employee, 'new_excessdate', null);
                    $employee->new_exitdocumentscompleted = array_get($ins_employee, 'new_exitdocumentscompleted', null);
                    $employee->new_exitmeetingdate = array_get($ins_employee, 'new_exitmeetingdate', null);
                    $employee->new_exittype = array_get($ins_employee, 'new_exittype.bValue', null);
                    $employee->new_finalexitdate = array_get($ins_employee, 'new_finalexitdate', null);
                    $employee->new_financialplanningsession = array_get($ins_employee, 'new_financialplanningsession') == "false" ? 'N' : 'Y';
                    $employee->ins_disability = array_get($ins_employee,'ins_disability') == "false" ? '0' : '1';
                    $employee->ins_reasonableadjustmentrequired = array_get($ins_employee,'ins_reasonableadjustmentrequired', null);
                    $employee->ins_culturallyandlinguisticallydiverse = array_get($ins_employee,'ins_culturallyandlinguisticallydiverse', null) == "false" ? '0' : '1';
                    if (!empty(array_get($ins_employee, 'new_firstname'))) {
                        if (empty($employee->new_firstname)) {
                            $employee->new_firstname = array_get($ins_employee, 'new_firstname');
                        }
                    }
                    $employee->new_forcedredundancyexitdate = array_get($ins_employee, 'new_forcedredundancyexitdate', null);
                    $employee->new_forcedredundancyexitmeetingdate = array_get($ins_employee, 'new_forcedredundancyexitmeetingdate', null);
                    $employee->new_forcedredundancyretentionstartdate = array_get($ins_employee, 'new_forcedredundancyretentionstartdate', null);
                    $employee->new_gender = array_get($ins_employee, 'new_gender.bValue', null);
                    $employee->new_hrcontact = array_get($ins_employee, 'new_hrcontact.bId', null);
                    $employee->new_hrvrrdinfosession = array_get($ins_employee, 'new_hrvrrdinfosession', null);
                    $employee->new_inductionattended = array_get($ins_employee, 'new_inductionattended') == "false" ? 'N' : 'Y';
                    $employee->new_inductionattendedreason = array_get($ins_employee, 'new_inductionattendedreason.bValue', null);
                    $employee->new_inductiondate = array_get($ins_employee, 'new_inductiondate', null);
                    $employee->new_inductiondeferreddate = array_get($ins_employee, 'new_inductiondeferreddate', null);
                    $employee->new_intentiontoforceretrenchletterissued = array_get($ins_employee, 'new_intentiontoforceretrenchletterissued') == "false" ? 'N' : 'Y';
                    $employee->new_jobclublevelmonday = array_get($ins_employee, 'new_jobclublevelmonday.bValue', null);
                    $employee->new_jobclubleveltuesday = array_get($ins_employee, 'new_jobclubleveltuesday.bValue', null);
                    $employee->new_jobclublevelwednesday = array_get($ins_employee, 'new_jobclublevelwednesday.bValue', null);
                    $employee->new_jobclublevelthursday = array_get($ins_employee, 'new_jobclublevelthursday.bValue', null);
                    $employee->new_jobclublevelfriday = array_get($ins_employee, 'new_jobclublevelfriday.bValue', null);
                    $employee->new_jobmatchingjobgrade = array_get($ins_employee, 'new_jobmatchingjobgrade.bId', null);
                    $employee->new_jobmatchingkeyskills = array_get($ins_employee, 'new_jobmatchingkeyskills', null);
                    $employee->new_jobmatchingqualifications = array_get($ins_employee, 'new_jobmatchingqualifications', null);
                    $employee->new_jobmatchingsalaryfrom = array_get($ins_employee, 'new_jobmatchingsalaryfrom.bValue', null);
                    $employee->new_jobmatchingsalaryto = array_get($ins_employee, 'new_jobmatchingsalaryto.bValue', null);
                    $employee->new_matchallcat = array_get($ins_employee, 'new_matchallcat') == "false" ? 'N' : 'Y';
                    $employee->new_meepaffected = array_get($ins_employee, 'new_meepaffected') == "false" ? 'N' : 'Y';
                    $employee->new_payrollcontact = array_get($ins_employee, 'new_payrollcontact.bId', null);
                    $employee->new_payrollgroup = array_get($ins_employee, 'new_payrollgroup.bValue', null);
                    $employee->new_personalcontactnumber = array_get($ins_employee, 'new_personalcontactnumber', null);
                    $employee->new_personalemail = array_get($ins_employee, 'new_personalemail', null);
                    if (!empty(array_get($ins_employee, 'new_personalhomenumber'))) {
                        if (empty($employee->new_personalhomenumber))
                            $employee->new_personalhomenumber = array_get($ins_employee, 'new_personalhomenumber');
                    }
                    if (!empty(array_get($ins_employee, 'new_personalmobilenumber'))) {
                        if (empty($employee->new_personalmobilenumber))
                            $employee->new_personalmobilenumber = array_get($ins_employee, 'new_personalmobilenumber');
                    }
                    $employee->new_previousattendee = array_get($ins_employee, 'new_previousattendee') == "false" ? 'N' : 'Y';
                    $employee->new_programtype = array_get($ins_employee, 'new_programtype.bValue', null);
                    $employee->new_pswregistration = array_get($ins_employee, 'new_pswregistration', null);
                    $employee->new_pvpostvrplans = array_get($ins_employee, 'new_pvpostvrplans.bValue', null);
                    $employee->new_pwaddressline1 = array_get($ins_employee, 'new_pwaddressline1', null);
                    $employee->new_pwaddressline2 = array_get($ins_employee, 'new_pwaddressline2', null);
                    if (isset($ins_employee['new_pwagency'])) {
                        $agencyDetails = AgencyDetails::where('ins_agency_id', $ins_employee['new_pwagency']['bId'])->first();
                        if ($agencyDetails)
                            $employee->new_pwagency = $agencyDetails->id;
                    }
                    $employee->new_pwagencybranch = array_get($ins_employee, 'new_pwagencybranch.bId', null);
                    $employee->new_pwcontactnumber = array_get($ins_employee, 'new_pwcontactnumber', null);
                    $employee->new_pwemail = array_get($ins_employee, 'new_pwemail', null);
                    $employee->new_pwemploymentstatus = array_get($ins_employee, 'new_pwemploymentstatus.bValue', null);
                    $employee->new_pwestimatesattached = array_get($ins_employee, 'new_pwestimatesattached') == "false" ? 'N' : 'Y';
                    $employee->new_pwexcessletterattached = array_get($ins_employee, 'new_pwexcessletterattached') == "false" ? 'N' : 'Y';
                    $employee->new_pwgrademaximumsalary = array_get($ins_employee, 'new_pwgrademaximumsalary.bValue', null);
                    $employee->new_pwgrademinimumsalary = array_get($ins_employee, 'new_pwgrademinimumsalary.bValue', null);
                    $employee->new_pwleavebalanceattached = array_get($ins_employee, 'new_pwleavebalanceattached') == "false" ? 'N' : 'Y';
                    $employee->new_pwmedicalissues = array_get($ins_employee, 'new_pwmedicalissues') == "false" ? 'N' : 'Y';
                    $employee->new_pwparttimehours = array_get($ins_employee, 'new_pwparttimehours', null);
                    $employee->new_pwperformanceissues = array_get($ins_employee, 'new_pwperformanceissues') == "false" ? 'N' : 'Y';
                    $employee->new_pwpositiongrade = array_get($ins_employee, 'new_pwpositiongrade.bName', null);
                    $employee->new_pwpositiontitle = array_get($ins_employee, 'new_pwpositiontitle.bName', null);
                    $employee->new_pwpostcode = array_get($ins_employee, 'new_pwpostcode', null);
                    $employee->new_pwpreviouspositiondescriptionattached = array_get($ins_employee, 'new_pwpreviouspositiondescriptionattached') == "false" ? 'N' : 'Y';
                    $employee->new_pwpreviouspositionhistoryattached = array_get($ins_employee, 'new_pwpreviouspositionhistoryattached') == "false" ? 'N' : 'Y';
                    $employee->new_pwrosterrequirements = array_get($ins_employee, 'new_pwrosterrequirements', null);
                    $employee->new_pwsalary = array_get($ins_employee, 'new_pwsalary.bValue', null);
                    $employee->new_pwservicestartdate = array_get($ins_employee, 'new_pwservicestartdate', null);
                    $employee->new_pwstate = array_get($ins_employee, 'new_pwstate.bValue', null);
                    $employee->new_pwsuburb = array_get($ins_employee, 'new_pwsuburb.bId', null);
                    $employee->new_pwtrainingrecordattached = array_get($ins_employee, 'new_pwtrainingrecordattached') == "false" ? 'N' : 'Y';
                    $employee->new_redeployedagencyid = array_get($ins_employee, 'new_redeployedagencyid.bId', null);
                    $employee->new_redeployedposition = array_get($ins_employee, 'new_redeployedposition', null);
                    $employee->new_redeployedsalary = array_get($ins_employee, 'new_redeployedsalary.bValue', null);
                    $employee->new_redeployedstartdate = array_get($ins_employee, 'new_redeployedstartdate', null);
                    $employee->new_redeployedsupervisorid = array_get($ins_employee, 'new_redeployedsupervisorid.bId', null);
                    $employee->new_redeployeepdactivated = array_get($ins_employee, 'new_redeployeepdactivated') == "false" ? 'N' : 'Y';
                    $employee->new_redeploymentpolicy = array_get($ins_employee, 'new_redeploymentpolicy.bValue', null);
                    $employee->new_redeploymentsource = array_get($ins_employee, 'new_redeploymentsource.bValue', null);
                    $employee->new_referraldate = array_get($ins_employee, 'new_referraldate', null);
                    $employee->new_registeredfortransitmatchingprogram = array_get($ins_employee, 'new_registeredfortransitmatchingprogram') == "false" ? 'N' : 'Y';
                    $employee->new_registeredtransitmatchingprogramdate = array_get($ins_employee, 'new_registeredtransitmatchingprogramdate', null);
                    $employee->new_residentialaddressline1 = array_get($ins_employee, 'new_residentialaddressline1', null);
                    $employee->new_residentialaddressline2 = array_get($ins_employee, 'new_residentialaddressline2', null);
                    $employee->new_residentialpostcode = array_get($ins_employee, 'new_residentialpostcode', null);
                    $employee->new_residentialstate = array_get($ins_employee, 'new_residentialstate.bValue', null);
                    $employee->new_residentialsuburbid = array_get($ins_employee, 'new_residentialsuburbid.bId', null);
                    $employee->new_resumecompleted = array_get($ins_employee, 'new_resumecompleted') == "false" ? 'N' : 'Y';
                    $employee->new_retentionenddate = array_get($ins_employee, 'new_retentionenddate', null);
                    $employee->new_skillsauditcomplete = array_get($ins_employee, 'new_skillsauditcomplete') == "false" ? 'N' : 'Y';
                    $employee->new_skillsauditcompletedate = array_get($ins_employee, 'new_skillsauditcompletedate', null);
                    if (!empty(array_get($ins_employee, 'new_surname'))) {
                        if (empty($employee->new_surname))
                            $employee->new_surname = array_get($ins_employee, 'new_surname');
                    }
                    $employee->new_title = array_get($ins_employee, 'new_title.bValue', null);
                    $employee->new_transitionactivity = array_get($ins_employee, 'new_transitionactivity.bValue', null);
                    $employee->new_transitionmanagerallocated = array_get($ins_employee, 'new_transitionmanagerallocated') == "false" ? 'N' : 'Y';
                    $employee->new_vreoidate = array_get($ins_employee, 'new_vreoidate', null);
                    $employee->new_vrexitdate = array_get($ins_employee, 'new_vrexitdate', null);
                    $employee->new_vrofferexcessdate = array_get($ins_employee, 'new_vrofferexcessdate', null);
                    $employee->new_vrofferextensionprovided = array_get($ins_employee, 'new_vrofferextensionprovided') == "false" ? 'N' : 'Y';
                    $employee->new_vrredeploymentchoice = array_get($ins_employee, 'new_vrredeploymentchoice.bValue', null);
                    $employee->new_willingtorelocate = array_get($ins_employee, 'new_willingtorelocate') == "false" ? 'N' : 'Y';
                    $employee->new_willingtorelocatedetails = array_get($ins_employee, 'new_willingtorelocatedetails', null);
                    $employee->new_yearsofservice = array_get($ins_employee, 'new_yearsofservice', null);
                    $employee->ownerid = array_get($ins_employee, 'ownerid.bId', null);
                    $employee->statecode = array_get($ins_employee, 'statecode.bValue', null);
                    $employee->transactioncurrencyid = array_get($ins_employee, 'transactioncurrencyid.bId', null);
                    $employee->save();
                    $user = User::where('email', $employee->new_personalemail)->first();
                    if(!$user){
                        $user_new = new User;
                        $password = md5(uniqid($employee->new_personalemail, true));
                        $user_new->password = $password;
                        $user_new->is_active = 0;
                        // Lets remove the soft delete so the user can login again!
                        $user_new->deleted_at = null;
                        $user_new->email = $employee->new_personalemail;
                        $user_new->first_name = $employee->new_firstname;
                        $user_new->last_name = $employee->new_surname;
                        $user_new->title = $employee->new_title;
                        $user_new->crm_user_id = $ins_employee['new_employeeid'];
                        $user_new->type = 'EiT';
                        $user_new->dashboard_profile = 1;
                        $user_new->save();
                        $this->saveLocations($user_new->id, $ins_employee);
                        $this->saveCategories($user_new->id, $ins_employee);
                        // @ToDo: Lets check if we the role is already attached.
                        $user_new->roles()->attach(3);
                        $employee->user_id = $user_new->id;
                        $employee->save();
                        $ctr_InsertedEmployee++;
                        if($employee->statecode == 0) {
                            $ins_programtype = array_get($ins_employee, 'ins_programtype.bValue', null);
                            if ((
                                    $employee->new_employeestatus == 100000005 ||
                                    $employee->new_employeestatus == 121660000 ||
                                    $employee->new_employeestatus == 121660001
                                ) && (
                                    $ins_programtype == 121660000 ||
                                    $ins_programtype == 121660001 ||
                                    $ins_programtype == 121660002
                                )) {
                                echo "User activation new email sent to: " . $employee->new_personalemail . "\n";
                                $this->emailService->sendUserActivationEmail($user_new);
                                $this->emailService->sendUserUploadRDEmail($user_new, $employee);
                            }//if
                        }
                    } else {
                        $user->email = $employee->new_personalemail;
                        $user->first_name= (!empty($employee->new_firstname)) ? $employee->new_firstname : $user->first_name;
                        $user->last_name = (!empty($employee->new_surname)) ? $employee->new_surname : $user->last_name;
                        $user->title = (!empty($employee->new_title)) ? $employee->new_title : $user->title;
                        $user->crm_user_id = (!empty($ins_employee['new_employeeid'])) ? $ins_employee['new_employeeid'] : $user->crm_user_id;
                        if ($employee->statecode == 1) {
                            $user->is_active = 0;
                        }
                        // Lets remove the soft delete so the user can login again!
                        $user->deleted_at = null;
                        $user->save();
                        $ctr_UpdatedEmployee++;
                        if(!empty($employee_old)) {
                            if($employee_old->new_employeestatus != $employee->new_employeestatus) {
                                $ins_programtype = array_get($ins_employee, 'ins_programtype.bValue', null);
                                if ((
                                        $employee->new_employeestatus == 100000005 ||
                                        $employee->new_employeestatus == 121660000 ||
                                        $employee->new_employeestatus == 121660001
                                    ) && (
                                        $ins_programtype == 121660000 ||
                                        $ins_programtype == 121660001 ||
                                        $ins_programtype == 121660002
                                    )) {
                                    $user_new = User::find($employee->user_id);
                                    // Only reset the user account again if the user is not active anymore.
                                    if (!empty($user_new) && $user_new->is_active == 0) {
                                        $password = md5(uniqid($employee->new_personalemail, true));
                                        $user_new->password = $password;
                                        $user_new->is_active = 0;
                                        $user_new->save();
                                        $ctr_ResetAccountEmployee++;
                                        echo "User activation existing email sent to: " . $user_new->email . "\n";
                                        $this->emailService->sendUserActivationEmail($user_new);
                                        $this->emailService->sendUserUploadRDEmail($user_new, $employee);
                                    }//if is_active is 0 send email
                                }//if
                            }//if
                        }//if old employee
                    }//else
                } catch (\Exception $e) {
                    logger($e->getMessage()." at ".$e->getFile()."@".$e->getCode());
                    logger($e->getTraceAsString());
                }
                if(!empty($user)){
                    $displayresiliencecourage = array_get($ins_employee, 'ins_displayresiliencecourage.bValue', 0);
                    $ins_actwithintegrity = array_get($ins_employee, 'ins_actwithintegrity.bValue', 0);
                    $ins_manageself = array_get($ins_employee, 'ins_manageself.bValue', 0);
                    $ins_valuediversity = array_get($ins_employee, 'ins_valuediversity.bValue', 0);
                    $ins_communicateeffectively = array_get($ins_employee, 'ins_communicateeffectively.bValue', 0);
                    $ins_committocustomerservice = array_get($ins_employee, 'ins_committocustomerservice.bValue', 0);
                    $ins_workcollaboratively = array_get($ins_employee, 'ins_workcollaboratively.bValue', 0);
                    $ins_influenceandnegotiate = array_get($ins_employee, 'ins_influenceandnegotiate.bValue', 0);
                    $ins_deliverresults = array_get($ins_employee, 'ins_deliverresults.bValue', 0);
                    $ins_planandprioritise = array_get($ins_employee, 'ins_planandprioritise.bValue', 0);
                    $ins_thinkandsolveproblems = array_get($ins_employee, 'ins_thinkandsolveproblems.bValue', 0);
                    $ins_demonstrateaccountability = array_get($ins_employee, 'ins_demonstrateaccountability.bValue', 0);
                    $ins_finance = array_get($ins_employee, 'ins_finance.bValue', 0);
                    $ins_technology = array_get($ins_employee, 'ins_technology.bValue', 0);
                    $ins_procurementandcontractmanagement = array_get($ins_employee, 'ins_procurementandcontractmanagement.bValue', 0);
                    $ins_projectmanagement = array_get($ins_employee, 'ins_projectmanagement.bValue', 0);
                    $ins_managedeveloppeople = array_get($ins_employee, 'ins_managedeveloppeople.bValue', 0);
                    $ins_inspiredirectionpurpose = array_get($ins_employee, 'ins_inspiredirectionpurpose.bValue', 0);
                    $ins_optimisebusinessoutcomes = array_get($ins_employee, 'ins_optimisebusinessoutcomes.bValue', 0);
                    $ins_managereformchange = array_get($ins_employee, 'ins_managereformchange.bValue', 0);
                    // Criteria
                    $ins_cb_displayresiliencecourage = array_get($ins_employee, 'ins_cb_displayresiliencecourage.bValue', 0);
                    $ins_cb_actwithintegrity = array_get($ins_employee, 'ins_cb_actwithintegrity.bValue', 0);
                    $ins_cb_manageself = array_get($ins_employee, 'ins_cb_manageself.bValue', 0);
                    $ins_cb_valuediversity = array_get($ins_employee, 'ins_cb_valuediversity.bValue', 0);
                    $ins_cb_communicateeffectively = array_get($ins_employee, 'ins_cb_communicateeffectively.bValue', 0);
                    $ins_cb_committocustomerservice = array_get($ins_employee, 'ins_cb_committocustomerservice.bValue', 0);
                    $ins_cb_workcollaboratively = array_get($ins_employee, 'ins_cb_workcollaboratively.bValue', 0);
                    $ins_cb_influenceandnegotiate = array_get($ins_employee, 'ins_cb_influenceandnegotiate.bValue', 0);
                    $ins_cb_deliverresults = array_get($ins_employee, 'ins_cb_deliverresults.bValue', 0);
                    $ins_cb_planandprioritise = array_get($ins_employee, 'ins_cb_planandprioritise.bValue', 0);
                    $ins_cb_thinkandsolveproblems = array_get($ins_employee, 'ins_cb_thinkandsolveproblems.bValue', 0);
                    $ins_cb_demonstrateaccountability = array_get($ins_employee, 'ins_cb_demonstrateaccountability.bValue', 0);
                    $ins_cb_finance = array_get($ins_employee, 'ins_cb_finance.bValue', 0);
                    $ins_cb_technology = array_get($ins_employee, 'ins_cb_technology.bValue', 0);
                    $ins_cb_procurementandcontractmanagement = array_get($ins_employee, 'ins_cb_procurementandcontractmanagement.bValue', 0);
                    $ins_cb_projectmanagement = array_get($ins_employee, 'ins_cb_projectmanagement.bValue', 0);
                    $ins_cb_managedeveloppeople = array_get($ins_employee, 'ins_cb_managedeveloppeople.bValue', 0);
                    $ins_cb_inspiredirectionpurpose = array_get($ins_employee, 'ins_cb_inspiredirectionpurpose.bValue', 0);
                    $ins_cb_optimisebusinessoutcomes = array_get($ins_employee, 'ins_cb_optimisebusinessoutcomes.bValue', 0);
                    $ins_cb_managereformchange = array_get($ins_employee, 'ins_cb_managereformchange.bValue', 0);
                    //Run capabilitymatch
                    $this->capabilitymatch($user->id, 1, $displayresiliencecourage, $ins_cb_displayresiliencecourage);
                    $this->capabilitymatch($user->id, 2, $ins_actwithintegrity, $ins_cb_actwithintegrity);
                    $this->capabilitymatch($user->id, 3, $ins_manageself, $ins_cb_manageself);
                    $this->capabilitymatch($user->id, 4, $ins_valuediversity, $ins_cb_valuediversity);
                    $this->capabilitymatch($user->id, 5, $ins_communicateeffectively, $ins_cb_communicateeffectively);
                    $this->capabilitymatch($user->id, 6, $ins_committocustomerservice, $ins_cb_committocustomerservice);
                    $this->capabilitymatch($user->id, 7, $ins_workcollaboratively, $ins_cb_workcollaboratively);
                    $this->capabilitymatch($user->id, 8, $ins_influenceandnegotiate, $ins_cb_influenceandnegotiate);
                    $this->capabilitymatch($user->id, 9, $ins_deliverresults, $ins_cb_deliverresults);
                    $this->capabilitymatch($user->id, 10, $ins_planandprioritise, $ins_cb_planandprioritise);
                    $this->capabilitymatch($user->id, 11, $ins_thinkandsolveproblems, $ins_cb_thinkandsolveproblems);
                    $this->capabilitymatch($user->id, 12, $ins_demonstrateaccountability, $ins_cb_demonstrateaccountability);
                    $this->capabilitymatch($user->id, 13, $ins_finance, $ins_cb_finance);
                    $this->capabilitymatch($user->id, 14, $ins_technology, $ins_cb_technology);
                    $this->capabilitymatch($user->id, 15, $ins_procurementandcontractmanagement, $ins_cb_procurementandcontractmanagement);
                    $this->capabilitymatch($user->id, 16, $ins_projectmanagement, $ins_cb_projectmanagement);
                    $this->capabilitymatch($user->id, 17, $ins_managedeveloppeople, $ins_cb_managedeveloppeople);
                    $this->capabilitymatch($user->id, 18, $ins_inspiredirectionpurpose, $ins_cb_inspiredirectionpurpose);
                    $this->capabilitymatch($user->id, 19, $ins_optimisebusinessoutcomes, $ins_cb_optimisebusinessoutcomes);
                    $this->capabilitymatch($user->id, 20, $ins_managereformchange, $ins_cb_managereformchange);
                }// if (!empty($user))
            } else {
                $ctr_InActiveInAva++;
                $crmIdOfInsEmployeeToBeDeactivated = array_get($ins_employee, 'new_employeeid', null);
                if(!empty($crmIdOfInsEmployeeToBeDeactivated)){
                    $insEmployeesMissingFlagTobeActivated[] = $crmIdOfInsEmployeeToBeDeactivated;
                }// if(!empty($crmIdOfInsEmployeeToBeDeactivated))
            }//else not activeinava
        }//foreach
        // Lets soft delete all the missing EITs
        $this->softDeleteAllMissingEitIds($employees);
        $this->softDeleteAllTheDeactivatedEit($insEmployeesMissingFlagTobeActivated);
        echo "Total employees fetched from CRM: ".$ctr_ParsedEmployee."\n";
        echo "Employees active in Ava: ".$ctr_ActiveInAva."\n";
        echo "\tNew accounts: ".$ctr_InsertedEmployee."\n";
        echo "\tUpdated accounts: ".$ctr_UpdatedEmployee."\n";
        echo "\tResetted accounts: ".$ctr_ResetAccountEmployee."\n";
        echo "Employees not active in Ava: ".$ctr_InActiveInAva."\n";
    }

    /**
     * @param array $insEmployeesMissingFlagTobeActivated
     */
    protected function softDeleteAllTheDeactivatedEit(array $insEmployeesMissingFlagTobeActivated) {
        logger('Trying to soft delete '. count($insEmployeesMissingFlagTobeActivated) . ' employees');
        $softDeleteUsers = \App\User::whereIn('crm_user_id', $insEmployeesMissingFlagTobeActivated)->where('type', 'EiT')->delete();
        logger('Total softDeleteAllTheDeactivatedEit ' . print_r($softDeleteUsers, true) . " deleted!");
        return $softDeleteUsers;
    }

    /**
     * This function soft deletes all users which are not found in the database any more.
     *
     * @param array $employees
     */
    protected function softDeleteAllMissingEitIds(array $employees) {
        // Lets load all the EITs
        // Les convert employees array into a collection object.
        $employeesCollection = collect($employees);
        // Lets pluck the eit
        $newEmployeeIds = $employeesCollection->pluck('new_employeeid');
        // Load all the users with missing EiTs and soft delete them.
        $softDeleteUsers = \App\User::whereNotIn('crm_user_id', $newEmployeeIds)->where('type', 'EiT')->delete();
        logger('Total softDeleteAllMissingEitIds ' . print_r($softDeleteUsers, true) . " deleted!");
        return $softDeleteUsers;
    }

    /**
     * @param $crmEmployeeId
     * @return mixed
     */
    protected function deactivateUserEmployeeId($crmEmployeeId) {
        return \App\User::where('crm_user_id', $crmEmployeeId)->where('type', 'EiT')->delete();
    }

    public function capabilitymatch($user_id, $name_id, $level, $criteria) {
        $user_chk = $this->userChkCapable($user_id, $name_id);
        if(!empty($user_chk)){
            $candidate_id = $user_chk->candidate_id;
            $id = $user_chk->id;
            if($user_id == $candidate_id){
                $candidate_capability = CandidateCapability::find($id);
                $candidate_capability->capability_name_id = $name_id;
                $candidate_capability->level_id = $level;
                $candidate_capability->criteria = $criteria;
                $candidate_capability->save();
            }
        } else {
            $candidate_capability = new CandidateCapability();
            $candidate_capability->candidate_id = $user_id;
            $candidate_capability->capability_name_id = $name_id;
            $candidate_capability->level_id = $level;
            $candidate_capability->criteria = $criteria;
            $candidate_capability->save();
        }
    }

    public function userChkCapable($user_id, $name_id) {
        return DB::table('ins_capability_candidate')->where('candidate_id', '=', $user_id)
            ->where('capability_name_id', '=', $name_id)
            ->select(['id', 'candidate_id'])
            ->first();
    }

    public function saveCategories($user_id, $ins_employee) {
        DB::table('ins_user_job_category_types')->where('user_id', '=', $user_id)->delete();
        for($i = 1; $i <= 8; $i++){
            $ins_jobcategory = array_get($ins_employee, 'ins_jobcategory'.$i.'.bId', null);
            if (!empty($ins_jobcategory)) {
                $id = DB::table('ins_job_category')->where('ins_job_category_id', '=', $ins_jobcategory)->first();
                if (!empty($id)) {
                    DB::table('ins_user_job_category_types')->insert(['user_id' => $user_id, 'job_category_type_id' => $id->id]);
                }
            }
        }
    }

    public function getDefaultCategoryType($ins_employee) {
        if(isset($ins_employee['new_defaultjobmatchingjobcategorytype']['bId'])){
            $jobcategorytype = $ins_employee['new_defaultjobmatchingjobcategorytype']['bId'];
            $jobcategorytypeid = DB::table('ins_job_category_types')->where('ins_job_category_type_id', '=', $jobcategorytype)->first();
            if (!empty($jobcategorytypeid)) {
                return $jobcategorytypeid->id;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function updateCategories($user_id, $ins_employee) {
        for($i = 1; $i <= 8; $i++){
            $ins_jobcategory = array_get($ins_employee, 'ins_jobcategory'.$i.'.bId', null);
            if(!empty($ins_jobcategory)){
                $id = DB::table('ins_job_category')->where('ins_job_category_id', '=', $ins_jobcategory)->first();
                if(!empty($id)){
                    $user_cat = DB::table('ins_user_job_category_types')
                        ->where('job_category_type_id', '=', $id->id)
                        ->where('user_id', '=', $user_id)
                        ->where('pending', '=', 1)
                        ->first();
                    if (!empty($user_cat)) {
                        DB::table('ins_user_job_category_types')->where('id', '=', $user_cat->id)->update(['pending' => 0]);
                    }
                }
            }
        }
    }

    public function saveLocations($user_id, $ins_employee) {
        DB::table('ins_user_job_locations')->where('user_id', '=', $user_id)->delete();
        for($i = 1; $i <= 4; $i++){
            $new_jobmatchinglocation = array_get($ins_employee, 'new_jobmatchinglocation'.$i.'.bId', null);
            $id = DB::table('ins_locations')->where('ins_location_id', '=', $new_jobmatchinglocation)->first();
            if (!empty($id)) {
                echo "user_id = " . $user_id . "\n";
                echo "ins_location_id = " . $id->id . "\n";
                DB::table('ins_user_job_locations')->insert(['user_id' => $user_id, 'ins_location_id' => $id->id]);
            }
        }
    }
}
