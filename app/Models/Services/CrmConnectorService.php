<?php

namespace App\Models\Services;

use App\Models\Crm\Traits\CrmConnectorTrait;

/**
 * Class CrmConnectorService
 * @package App\Models\Services
 */
class CrmConnectorService {
    use CrmConnectorTrait;

    /**
     * CrmConnectorService constructor.
     */
    public function __construct(){
        $this->loadCrmConnectorConfig();
    }
}
