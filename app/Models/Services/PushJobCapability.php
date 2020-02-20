<?php

namespace App\Models\Services;

use App\Models\Crm\CrmConnector;
use Illuminate\Support\Facades\Config;
use App\Models\Repositories\CrmLookupDataImportRepository;
use App\Models\Repositories\PushJobCapabilityRepository;

/**
 * Class PushJobCapability
 * @package App\Models\Services
 */
class PushJobCapability extends CrmConnectorService {
    private $JobCapabilityRepository;

    /**
     * PushJobCapability constructor.
     */
    public function __construct(){
        parent::__construct();
        $this->JobCapabilityRepository = new PushJobCapabilityRepository();
    }

    /**
     *
     */
    public function pushCapability() {
        $job_ids = $this->JobCapabilityRepository->getJobCapabilityIDs();
        foreach ($job_ids as $job_id) {

            $jobid = $this->JobCapabilityRepository->getJobID($job_id->job_id);
            if (!empty($jobid)) {
                $jobid = $jobid->jobid;
                $jobCapability = $this->JobCapabilityRepository->getJobCapability($job_id->job_id);
                $insLocations = $this->pushJobApplied($jobCapability, $jobid);
            }
        }
    }

    public function pushJobApplied($jobCapability, $jobid) {
        foreach ($jobCapability as $jobcabs) {
            $status = $jobcabs->core_status == 1 ? 'true' : 'false';
            $fields = [
                ['name' => $jobcabs->crm_match_names, 'value' => $jobcabs->level_id, 'type' => 'option'],
                ['name' => $jobcabs->crm_match_core_status, 'value' => $status, 'type' => 'boolean'],
            ];
            $result = $this->getCrmConnector()->getController()->updateEntity('new_job', $jobid, $fields);
        }
        return $result;
    }
}
