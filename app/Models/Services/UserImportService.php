<?php
namespace App\Models\Services;

use App\Models\Crm\CrmConnector;
use App\Models\Repositories\UserImportRepository;
use Illuminate\Support\Facades\Config;

/**
 * Class UserImportService
 * @package App\Models\Services
 */
class UserImportService extends CrmConnectorService {
    private $userImportRepository;

    /**
     * UserImportService constructor.
     * @param UserImportRepository $userImportRepository
     */
    function __construct(UserImportRepository $userImportRepository) {
        parent::__construct();
        $this->userImportRepository = $userImportRepository;
    }

    /**
     *
     */
    public function processUserImport() {
        $insEmployees = $this->getInsEmployees();
        $this->userImportRepository->importEmployees($insEmployees);
    }

    /**
     * @return array
     */
    private function getInsClients() {
        return $this->getCrmConnector()->getController()->getAllEntityRecords('ins_client');
    }

    /**
     * @return array
     */
    private function getInsEmployees() {
        return $this->getCrmConnector()->getController()->getAllEntityRecords('new_employee', 'new_employeeid');
    }

    public function synchWithPortalUserCategory(){
        $insEmployees = $this->getInsEmployees();
        $this->userImportRepository->updateEmployeesCategory($insEmployees);
    }
}
