<?php
namespace App\Models\Crm\Traits;

use App\Models\Crm\CrmConnector;
use Illuminate\Support\Facades\Config;

/**
 * Trait CrmConnector
 * @package App\Models\Crm\Traits
 */
trait CrmConnectorTrait {
    /**
     * @var string
     */
    protected $crm_url;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var CrmConnector
     */
    protected $_crmConnector = null;

    /**
     * Service constructor.
     */
    protected function loadCrmConnectorConfig() {
        // Lets load the environment label
        $env = \App::environment();
        // Lets load default CRM config details
        $this->crm_url = Config::get('crm.url');
        $this->username = Config::get('crm.username');
        $this->password = Config::get('crm.password');

        // Lets change the details for certain environments
        if ($env == 'sandbox' || $env == 'local') {
            $this->crm_url = Config::get('crmsandbox.url');
            $this->username = Config::get('crmsandbox.username');
            $this->password = Config::get('crmsandbox.password');
        } elseif ($env == 'production') {
            $this->crm_url = Config::get('crmprod.url');
            $this->username = Config::get('crmprod.username');
            $this->password = Config::get('crmprod.password');
        }
    }

    /**
     * @return CrmConnector
     */
    protected function getCrmConnector() {
        if (is_null($this->_crmConnector)) {
            $this->loadCrmConnectorConfig();
            echo "Loading config values...\n";
            echo "CRM url: ".$this->crm_url."\n";
            echo "CRM username: ".$this->username."\n";
            echo "CRM password: ".$this->password."\n";
            $this->_crmConnector = new CrmConnector($this->crm_url, $this->username, $this->password);
        }
        return $this->_crmConnector;
    }
}
