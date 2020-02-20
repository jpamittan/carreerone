<?php

namespace App\Models\Services;

use App\Models\Crm\CrmConnector;
use Illuminate\Support\Facades\Config;
use App\Models\Repositories\jobMatchImportRepository;

class JobMatchImportService extends CrmConnectorService {
    /**
     * @var jobMatchImportRepository
     */
    private $jobMatchImportRepository;

    /**
     * JobMatchImportService constructor.
     * @param jobMatchImportRepository $jobMatchImportRepository
     */
    public function __construct(jobMatchImportRepository $jobMatchImportRepository) {
        parent::__construct();
        $this->jobMatchImportRepository = $jobMatchImportRepository;
    }

    /**
     *
     */
    public function processJobRDUpload() {
        $insJobMatch = $this->getInsJobs();
        $this->jobMatchImportRepository->checkJobRDUpload($insJobMatch);
    }

    /**
     *
     */
    public function processJobMatchImport() {
        $insJobMatch = $this->getInsJobs();
        $this->jobMatchImportRepository->importJobMatch($insJobMatch);
    }

    /**
     * @return array
     */
    private function getInsJobs() {
        return $this->getCrmConnector()->getController()->getAllEntityRecords('new_jobmatched',
            'new_jobmatchedid', null, [
                [
                    'field' => 'statecode',
                    'operator' => 'Equal',
                    'value_type' => 'int',
                    'value' => 0
                ]
            ]);
    }

    /**
     *
     */
    public function processJobMatchEOIImport() {
        $insJobMatch = $this->getInsJobsEOI();
        $this->jobMatchImportRepository->importJobMatchEOI($insJobMatch);
    }

    /**
     * @return array
     */
    private function getInsJobsEOI() {
        return $this->getCrmConnector()->getController()->getAllEntityRecords('new_jobmatched','new_jobmatchedid');
    }
}
