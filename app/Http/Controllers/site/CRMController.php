<?php

namespace App\Http\Controllers\site;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\Http\Controllers\site\DynamicsClient;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Sixdg\DynamicsCRMConnector\Controllers\DynamicsCRMController;
use Sixdg\DynamicsCRMConnector\DynamicsCRMConnector;
use Sixdg\DynamicsCRMConnector\Queries\FetchXML;
use Sixdg\RedisCache\Client;
use Sixdg\DynamicsCRMConnector\AspectKernel\ApplicationAspectKernel;
use Config, DB, View, Validator, Response,Redirect;

class CRMController extends Controller {
    /**
     * @var DynamicsCRMController
     */
    protected $controller;

    /**
     * @var DynamicsCRMConnector
    */
    protected $connector;
    protected $requester;
    protected $requestBuilder;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
    */
    public function getdata() {
        define('MS_CRM_URL', Config::get('crm.url'));
        define('MS_CRM_USER', Config::get('crm.username'));
        define('MS_CRM_PASS', Config::get('crm.password'));
        $method = '/api/data/v8.1/';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, MS_CRM_URL . $method);
        curl_setopt($ch, CURLOPT_USERPWD, MS_CRM_USER .':'. MS_CRM_PASS);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept' => 'application/json',
            'OData-MaxVersion' => '4.0',
            'OData-Version' => '4.0',
            'Content-Type' => 'application/json',
        ));
        $server_output = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $json = array();
        if ((int)$status_code === 200) {
            $json = json_decode($server_output);
        }
        return $json;
    }
}