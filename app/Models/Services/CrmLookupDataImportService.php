<?php

namespace App\Models\Services;

use App\Models\Crm\CrmConnector;
use Illuminate\Support\Facades\Config;
use App\Models\Repositories\JobsImportRepository;
use App\Models\Repositories\CrmLookupDataImportRepository;

class CrmLookupDataImportService extends CrmConnectorService {
    private $jobsImportRepository;
    private $sandbox;

    /**
     * @var CrmLookupDataImportRepository
     */
    protected $crmLookupDataImportRepository;

    function __construct(CrmLookupDataImportRepository $crmLookupDataImportRepository) {
        parent::__construct();
        $this->crmLookupDataImportRepository = $crmLookupDataImportRepository;
    }

    public function processCrmLookupData() {
        $insSuburbs = $this->getInsSuburbs();
        $this->crmLookupDataImportRepository->importSuburbs($insSuburbs);
        $insSkills = $this->getInsSkills();
        $this->crmLookupDataImportRepository->importSkills($insSkills);
        $insLocations = $this->getInsLocations();
        $this->crmLookupDataImportRepository->importLocations($insLocations);
        $insJobCategoryTypes = $this->getInsCategoryTypes();
        $this->crmLookupDataImportRepository->importJobCategoryTypes($insJobCategoryTypes);
        $insJobCategories = $this->getInsCategories();
        $this->crmLookupDataImportRepository->importJobCategories($insJobCategories);
        $insAgencies = $this->getInsAgencies();
        $this->crmLookupDataImportRepository->importAgencies($insAgencies);
        // Lets import all the Agency branches after we have added or new Agencies
        app('App\Models\Services\AgencyService')->importAgencyBranchesFromCRM();
    }

    /**
     * @return array
     */
    private function getInsCategories() {
        echo "Processing getInsCategories \n";
        return $this->getCrmConnector()->getController()->getAllEntityRecords('new_jobcategory','new_jobcategoryid');

    }

    /**
     * @return array
     */
    private function getInsCategoryTypes() {
        echo "Processing getInsCategoryTypes \n";
        return $this->getCrmConnector()->getController()->getAllEntityRecords('new_jobcategorytype','new_jobcategorytypeid');
    }

    /**
     * @return array
     */
    private function getInsLocations() {
        echo "Processing getInsLocations \n";
        return $this->getCrmConnector()->getController()->getAllEntityRecords('new_location','new_locationid');

    }

    /**
     * @return array
     */
    private function getInsSuburbs() {
        echo "Processing getInsSuburbs \n";
        return $this->getCrmConnector()->getController()->getAllEntityRecords('new_suburb','new_suburbid');

    }

    /**
     * @return array
     */
    private function getInsAgencies() {
        echo "Processing getInsAgencies & Agency branches \n";
        return $this->getCrmConnector()->getController()->getAllEntityRecords('new_agency','new_agencyid');
    }

    /**
     * @return array
     */
    private function getInsSkills() {
        echo "Processing getInsSkills \n";
        return $this->getCrmConnector()->getController()->getAllEntityRecords('ins_skill','ins_skillid');
    }
}
