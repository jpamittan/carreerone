<?php

namespace App\Models\Services;

use App\Models\Entities\AgencyBranch;
use App\Models\Entities\AgencyDetails;

/**
 * Class AgencyService
 * @package App\Models\Services
 */
class AgencyService extends CrmConnectorService {
    /**
     * @param array $agencyBranchData
     * @param null $agencyCrmId
     *
     * @return bool
     */
    public function createAgencyBranch(array $agencyBranchData, $agencyCrmId = null)
    {
        if (is_null($agencyCrmId)) {
            // Lets find the Agency CRM ID in the agencyBranchDAta
            $agencyCrmId = !empty($agencyBranchData['new_agencyid']) && !empty($agencyBranchData['new_agencyid']['bId'])
                ? $agencyBranchData['new_agencyid']['bId'] : null;
        }
        if (!empty($agencyCrmId) && !empty($agencyBranchData['new_name'])) {
            // Lets find the Agency from with CRM ID in the database
            $agency = $this->findAgencyWithCRMId($agencyCrmId);

            if (!empty($agency)) {
                // Lets create the map for the Agency Branch
                $agencyBranch = AgencyBranch::firstOrNew([
                    'agency_id' => $agency->id,
                    'location_name' => $agencyBranchData['new_name']
                ]);
                return $agencyBranch->save();
            }
        }
        return false;
    }

    public function importAgencyBranchesFromCRM() {
        $agencyBranches = $this->getInsAgencyBranches();
        foreach ($agencyBranches as $agencyBranch) {
            $this->createAgencyBranch($agencyBranch);
        }
    }

    /**
     * @return array
     */
    protected function getInsAgencyBranches() {
        return $this->getCrmConnector()->getController()->getAllEntityRecords('new_agencybranch','new_agencybranchid');
    }

    /**
     * @param $agencyCrmId
     * @return null
     */
    protected function findAgencyWithCRMId($agencyCrmId) {
        return !empty($agencyCrmId) ? AgencyDetails::where(['ins_agency_id' => $agencyCrmId])->first() : null;
    }
}
