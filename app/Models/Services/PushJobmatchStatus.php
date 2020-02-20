<?php

namespace App\Models\Services;

use App\Models\Crm\CrmConnector;
use Illuminate\Support\Facades\Config;
use App\Models\Repositories\CrmLookupDataImportRepository;
use App\Models\Repositories\PushJobCapabilityRepository;

/**
 * Class PushJobmatchStatus
 * @package App\Models\Services
 */
class PushJobmatchStatus extends CrmConnectorService {
    private $PushJobCapabilityRepository;

    /**
     * PushJobmatchStatus constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $jobmatchid
     * @param $status
     *
     * @return array
     */
    public function pushJobMatchStatus($jobmatchid, $status) {
        $fields = [
            ['name' => 'ins_matchstatus', 'value' => $status, 'type' => 'option'],
        ];

        return $this->getCrmConnector()->getController()->updateEntity('new_jobmatched', $jobmatchid, $fields);
    }

    /**
     * @param $jobid
     * @param $field
     * @param $status
     * @param $type
     *
     * @return array
     */
    public function pushJobStatus($jobid, $field, $status, $type) {
        $fields = [
            ['name' => $field, 'value' => $status, 'type' => $type],
        ];
        return $this->getCrmConnector()->getController()->updateEntity('new_job', $jobid, $fields);
    }

    /**
     * @param $jobid
     * @param $field
     * @param $status
     * @param $type
     *
     * @return array
     */
    public function pushJobMatchStatusIndividual($jobid, $field, $status, $type) {
        $fields = [
            ['name' => $field, 'value' => $status, 'type' => $type],
        ];
        return $this->getCrmConnector()->getController()->updateEntity('new_jobmatched', $jobid, $fields);
    }

    /**
     * @param $jobid
     * @param $fields
     * @return array
     */
    public function pushJobMatchStatusIndividualMultiple($jobid, $fields) {
        return $this->getCrmConnector()->getController()->updateEntity('new_jobmatched', $jobid, $fields);
    }

    /**
     * @param $jobid
     * @param $fields
     * @return array
     */
    public function pushJobStatuses($jobid, $fields) {
        return $this->getCrmConnector()->getController()->updateEntity('new_job', $jobid, $fields);
    }

    /**
     * @param $jobid
     * @param $employeeid
     * @param $job_title
     * @return array
     */
    public function pushJobEoiStatus($jobid, $employeeid, $job_title) {
        //RD received – EiT has expressed interest
        $fields = [
            ['name' => 'new_eitid', 'value' => $employeeid, 'type' => 'entity', 'entity_name' => 'new_employee'],
            ['name' => 'new_jobid', 'value' => $jobid, 'type' => 'entity', 'entity_name' => 'new_job'],
            ['name' => 'ins_matchstatus', 'value' => 121660005, 'type' => 'option'],
            ['name' => 'new_name', 'value' => $job_title, 'type' => 'string'],

        ];
        return $this->getCrmConnector()->getController()->createEntity('new_jobmatched', $fields);
    }

    /**
     * @param $jobid
     * @param $employeeid
     * @param $job_title
     * @param $comments
     *
     * @return array
     */
    public function pushJobEoiRejectedStatus($jobid, $employeeid, $job_title, $comments) {
        //RD received – EiT has expressed interest
        $fields = [
            ['name' => 'new_eitid', 'value' => $employeeid, 'type' => 'entity', 'entity_name' => 'new_employee'],
            ['name' => 'new_jobid', 'value' => $jobid, 'type' => 'entity', 'entity_name' => 'new_job'],
            ['name' => 'ins_matchstatus', 'value' => 121660006, 'type' => 'option'],
            ['name' => 'new_comment', 'value' => $comments, 'type' => 'string'],
            ['name' => 'new_name', 'value' => $job_title, 'type' => 'string'],
        ];
        return $this->getCrmConnector()->getController()->createEntity('new_jobmatched', $fields);
    }
}
