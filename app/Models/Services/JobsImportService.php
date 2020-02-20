<?php

namespace App\Models\Services;

use App\Models\Crm\CrmConnector;
use Illuminate\Support\Facades\Config;
use App\Models\Repositories\JobsImportRepository;

/**
 * Class JobsImportService
 * @package App\Models\Services
 */
class JobsImportService extends CrmConnectorService {
    private $jobsImportRepository;

    /**
     * JobsImportService constructor.
     * @param JobsImportRepository $jobsImportRepository
     */
    function __construct(JobsImportRepository $jobsImportRepository) {
        parent::__construct();
        $this->jobsImportRepository = $jobsImportRepository;
    }

    /**
     *
     */
    public function processJobsImport() {
        $insJobs = $this->getInsJobs();
        $this->jobsImportRepository->importJobs($insJobs);
    }

    /**
     * @return array
     */
    private function getInsJobs() {
        $result = $this->getCrmConnector()->getController()->getAllEntityRecords('new_job', 'new_jobid');
        return $result;
    }
}
