<?php

namespace App\Models\Services;

use App\Models\Crm\CrmConnector;
use App\Models\Entities\CapabilityMatch;
use Illuminate\Support\Facades\Config;
use App\Models\Repositories\CrmLookupDataImportRepository;
use App\Models\Repositories\PushUserCapabilityRepository;
use App\Models\Repositories\CapabilityMatchRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class PushUserCapability
 * @package App\Models\Services
 */
class PushUserCapability extends CrmConnectorService {

    /**
     * @var PushUserCapabilityRepository
     */
    private $PushUserCapabilityRepository;

    public function __construct() {
        parent::__construct();
        $this->PushUserCapabilityRepository = new PushUserCapabilityRepository();
    }

    public function pushCapabilityGap() {
        $user_ids = $this->PushUserCapabilityRepository->getUserCapabilityIDs();
        foreach ($user_ids as $user_id) {
            $service = new JobAssetService();
            $cap_match_repo = new CapabilityMatchRepository();
            $u = new \stdClass;
            $u->candidate_id = $user_id->candidate_id;
            $crmuser = \DB::table('users')->where("id", '=', $u->candidate_id)->first();
            $user_capabilities = $cap_match_repo->getCandidateCapabilitiesCriteria($u);
            $user_jobs = $service->getCandidateJobs($u->candidate_id);
            $user_capabilities = $service->getMismatches($user_capabilities, $user_jobs);
            $fields = array();
            if (isset($user_capabilities)) {
                foreach ($user_capabilities as $cap1) {
                    foreach ($cap1 as $cap2) {
                        foreach ($cap2 as $cap) {
                            if (isset($cap->mismatch) && count($cap->mismatch)) {
                                $per = round(count($cap->mismatch) / (count($cap->match) + count($cap->mismatch)) * 100);
                                $cap_name = \DB::table('ins_capability_match_names')->where("id", '=', $cap->capability_name_id)->first();
                                $fields[] = ['name' => $cap_name->crm_gap_per, 'value' => $per, 'type' => 'decimal'];
                            }
                        }
                    }
                }
            }
            if (!empty($fields)) {
                $userID = $crmuser->crm_user_id;
                $result = $this->pushCapabilityGapMultiple($userID, $fields);
            }
        }
    }

    /**
     * @param $userID
     * @param $fields
     *
     * @return array
     */
    public function pushCapabilityGapMultiple($userID, $fields) {
        return $this->getCrmConnector()->getController()->updateEntity('new_employee', $userID, $fields);
    }


    public function pushCapability() {
        $user_ids = $this->PushUserCapabilityRepository->getUserCapabilityIDs();
        foreach ($user_ids as $user_id) {
            $userCapability = $this->PushUserCapabilityRepository->getUserCapability($user_id->candidate_id);
            $userID = $this->PushUserCapabilityRepository->getUserID($user_id->candidate_id);
        }
    }

    /**
     * @param $userCapability
     * @param $userID
     *
     * @return array|string
     */
    public function pushUserCapaba($userCapability, $userID) {
        $result = '';
        foreach ($userCapability as $usercabs) {
            $level_id = $usercabs->level_id != 0 ? $usercabs->level_id : 'null';
            $fields = [
                ['name' => $usercabs->crm_user_names, 'value' => $level_id, 'type' => 'option'],
            ];
            if(!empty($usercabs->core) && $usercabs->core) {
                $capabilityMatchName = DB::table('ins_capability_match_names')
                    ->where('id', '=',$usercabs->capability_name_id)->first();;
                if(!empty($capabilityMatchName) && !empty($capabilityMatchName->crm_user_core_status)) {
                    $fields[] = ['name' => $capabilityMatchName->crm_user_core_status, 'value' => true, 'type' => 'boolean'];
                }
            }
            $result = $this->getCrmConnector()->getController()->updateEntity('new_employee', $userID, $fields);
        }
        return $result;
    }
}
