<?php

namespace App\Models\Services;

use App\Models\Crm\CrmConnector;
use App\Models\Entities\CaseManager;
use Illuminate\Support\Facades\Config;
use App\Models\Repositories\CaseManagerRepository;

/**
 * Class CaseManagerService
 * @package App\Models\Services
 */
class CaseManagerService extends CrmConnectorService {
    private $CaseManagerRepository;

    /**
     * CaseManagerService constructor.
     * @param CaseManagerRepository $CaseManagerRepository
     */
    public function __construct(CaseManagerRepository $CaseManagerRepository) {
        parent::__construct();
        $this->CaseManagerRepository = $CaseManagerRepository;
    }

    /**
     *
     */
    public function processCaseManager() {
        $casemanager_users = $this->getCaseManager();
        $this->CaseManagerRepository->importcasemangers($casemanager_users);
    }

    /**
     * @return array
     */
    private function getCaseManager() {
        return $this->getCrmConnector()->getController()->getAllEntityRecords('systemuser','systemuserid');
    }

    /**
     * This function updates or creates a new Case manager in the database.
     *
     * @param $crmId
     * @param $email
     * @param null $fullName
     *
     * @return bool
     */
    public function registerOrUpdateCaseManager($crmId, $email, $fullName = null) {
        if (!empty($crmId) && !empty($email)) {
            // Lets search if the Case Manager is exists
            $caseManager = CaseManager::firstOrNew([
                'systemuserid' => $crmId,
                'internalemailaddress' => $email,
            ]);
            if (!empty($fullName)) {
                $caseManager->fullname = $fullName;
            }
            return $caseManager->save();
        }
        return false;
    }
}
