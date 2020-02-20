<?php

namespace App\Models\Services;

use App\Models\Crm\CrmConnector;
use Illuminate\Support\Facades\Config;
use App\Models\Repositories\CrmLookupDataImportRepository;
use App\Models\Repositories\CrmSyncDataRepository;
use App\Models\Repositories\jobMatchImportRepository;
use App\Models\Services\CaseManagerAssetService;
use App\Models\Services\PushJobmatchStatus;

class CrmSyncDataService extends CrmConnectorService {
    private $crmSyncDataRepository;

    /**
     * @var CrmSyncDataRepository
     */
    protected $crmLookupDataImportRepository;

    /**
     * @var jobMatchImportRepository
     */
    protected $jobMatchImportRepository;

    /**
     * CrmSyncDataService constructor.
     * @param CrmSyncDataRepository $crmSyncDataRepository
     */
    function __construct(CrmSyncDataRepository $crmSyncDataRepository) {
        parent::__construct();
        $this->crmLookupDataImportRepository = $crmSyncDataRepository;
        $this->jobMatchImportRepository = new jobMatchImportRepository;
    }

    public function updateInterviewConfirmation($data) {
        $this->pushInterviewConfirmation($data);
    }

    public function synchWithCrm() {
        $jobApplied = $this->crmLookupDataImportRepository->getJobAppliedUpdate();
        $insLocations = $this->pushJobApplied($jobApplied);
        //push capanbility score
        $push_cap = $this->pushJobMatchedCapabilities();
    }

    public function synchWithPortalDraft() {
        $insJobMatchDraft = $this->getInsJobsAppliedDraft();
        $this->jobMatchImportRepository->importJobsAppliedDraft($insJobMatchDraft);
    }

    public function synchWithPortalJobsApplied() {
        $insJobMatchDraft = $this->getInsJobsApplied();
        $this->jobMatchImportRepository->importJobsINSProgress($insJobMatchDraft);
    }

    private function getInsJobsApplied() {
        $connector = new CrmConnector($this->crm_url, $this->username, $this->password);
        $controller = $connector->getController();
        $result = $controller->getAllEntityRecords('new_jobapplied', 'new_jobappliedid');
        return $result;
    }

    private function getInsJobsAppliedDraft() {
        $result = $this->getCrmConnector()->getController()->getAllEntityRecords('new_jobapplied', 'new_jobappliedid');
        return $result;
    }

    private function pushJobMatchedCapabilities() {
        $service = new CaseManagerAssetService();
        $jobmatch = \DB::table('ins_jobmatch')->get();
        $jobmatch_stat = new PushJobmatchStatus();
        foreach ($jobmatch as $j) {
            $user_id = $j->candidate_id;
            $job_id = $j->job_id;
            $jobid = $j->new_jobmatchedid;
            $capability_score = $service->getCapabilityScore($job_id, $user_id);
            if (isset($capability_score) && $capability_score > 0) {
                $fields =
                    array(
                        ['name' => 'ins_capabilityscore', 'value' => $capability_score, 'type' => 'decimal'],
                    );
                $res = $jobmatch_stat->pushJobMatchStatusIndividualMultiple($jobid, $fields);
            }
        }
    }

    private function pushJobApplied($jobApplied) {
        $controller = $this->getCrmConnector()->getController();
        foreach ($jobApplied as $jobApp) {
            $userid = $jobApp->candidate_id;;
            $jobid = $jobApp->job->id;;
            $jobApp1 = \DB::table('ins_job_candidate')->where('ins_job_candidate.job_id', '=', $jobid)
                ->where('ins_job_candidate.candidate_id', '=', $userid)
                ->where('ins_job_candidate.ins_pushed', '=', 'N')
                ->orderBy('id', 'desc')->limit(1)->first();
            $ins_job_apply_id = $jobApp->new_jobmatchedid;
            $job_det = $this->crmLookupDataImportRepository->getJobDet($jobApp->job_id);
            if (!empty($job_det)) {
                if ($jobApp->submit_status == 1) {
                    $status = 12166000;
                } elseif ($jobApp->submit_status == 0) {
                    $status = 121660001;
                }
                $deadline_date = date("d-m-Y", strtotime($job_det->deadline_date));
                try {
                    $employeeid = isset($jobApp->user->employee->employeeid) ? $jobApp->user->employee->employeeid : 0;
                    $jobid = isset($jobApp->job->jobid) ? $jobApp->job->jobid : 0;
                    $job_title = isset($jobApp->job->job_title) ? $jobApp->job->job_title : '';
                    $new_firstname = isset($jobApp->user->employee->firstnam) ? $jobApp->user->employee->new_firstnam : '';
                    if ($employeeid != '' && $jobid != '') {
                        $fields = [
                            ['name' => 'new_outcome', 'value' => '100000002', 'type' => 'option'],
                            ['name' => 'ins_progress', 'value' => $status, 'type' => 'option'],
                        ];
                        $result = $controller->updateEntity('new_jobapplied', $ins_job_apply_id, $fields);
                        $jobApp->ins_pushed = 'Y';
                        $jobApp->save();
                        if (!empty($jobApp1)) {
                            \DB::table('ins_job_candidate')->where('id', '=', $jobApp1->id)
                                ->update(['ins_job_apply_id' => $ins_job_apply_id,
                                    'ins_pushed' => 'Y',
                                ]);
                        }
                    }
                } catch (\Exception $e) {
                    print_r($e->getMessage());
                }
            }
        }
        return $result;
    }

    public function pushInterviewConfirmation($data) {
        $ins_job_apply_id = $data['ins_job_apply_id'];
        $interviewconfirmed = $data['interviewconfirmed'];
        $interviewdate = $data['interviewdate'];
        $interviewdetails = htmlentities($data['interviewdetails']);
        $address_instruciton = htmlentities($data['comments']);
        $panel_member = htmlentities($data['panel_member']);
        try {
            //TODO Panel Member
            $fields = [
                ['name' => 'new_interviewconfirmed', 'value' => $interviewconfirmed, 'type' => 'boolean'],
                ['name' => 'new_interviewdate', 'value' => $interviewdate, 'type' => 'dateTime'],
                ['name' => 'ins_convenorpanelmembers', 'value' => $panel_member, 'type' => 'string'],
                ['name' => 'new_interviewdetails', 'value' => $address_instruciton, 'type' => 'string'],
                ['name' => 'new_comment', 'value' => $interviewdetails, 'type' => 'string'],
                ['name' => 'ins_progress', 'value' => 121660006, 'type' => 'option'],
            ];
            $result = $this->getCrmConnector()->getController()->updateEntity('new_jobapplied', $ins_job_apply_id, $fields);
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function getInsSkills() {
        return $this->getCrmConnector()->getController()->getAllEntityRecords('ins_skill','ins_skillid');
    }
}
