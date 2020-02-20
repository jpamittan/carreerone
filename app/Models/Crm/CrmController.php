<?php
namespace App\Models\Crm;

use App\Models\Crm\CrmConnector;
use App\Models\Crm\Response\CrmEntityResponse;
use App\Models\Crm\Response\CrmMultipleEntityResponse;
use App\Models\Crm\Response\CrmCreateResponse;
use App\Models\Crm\SoapGenerator\SoapGenerator;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class CrmController {
	private $connector;
	private $found = null;
	function __construct(CrmConnector $connector) {
        echo "CRM controller constructor inialized...\n";
		$this->connector = $connector;
		$this->soapGenerator = new SoapGenerator();
		$this->soapRequester = new CrmExecuteSoap();
	}

    /**
     * This funciton creates an entity in the CRM
     *
     * @param $entityName
     * @param array $fields
     *
     * @return array
     */
    public function createEntity($entityName, array $fields) {
        $action = 'Create';
        $params = [
            "entity" => $entityName,
            "fields" => $fields
        ];
        $request = $this->soapGenerator->generateSoap($action, $params);
        $res = $this->soapRequester->ExecuteSOAPRequest(
            $this->connector->getAuthHeader(),
            $request,
            $this->connector->getUrl()
        );
        $crmEntityRes = new CrmCreateResponse($res);
        $entity = $crmEntityRes->getResults();
        return $entity;
    }

	public function updateEntity($entityName, $entity_id, array $fields) {
		$action = 'Update';
		$params = [
			"entity"=>$entityName,
			"entity_id"=>$entity_id,
			"fields"=>$fields
		];
		$request = $this->soapGenerator->generateSoap($action,$params);
		$res = 	$this->soapRequester->ExecuteSOAPRequest(
			$this->connector->getAuthHeader(),
			$request,
			$this->connector->getUrl()
		);
		$crmEntityRes = new CrmCreateResponse($res);
		$entity = $crmEntityRes->getResults();
		return $entity;
	}

	public function deleteEntity($entityName, $entity_id) {
		$action = 'Delete';
		$params = [
			"entity"=>$entityName,
			"entity_id"=>$entity_id,
		];
		$request = $this->soapGenerator->generateSoap($action,$params);
		$res = 	$this->soapRequester->ExecuteSOAPRequest(
    		$this->connector->getAuthHeader(),
    		$request,
    		$this->connector->getUrl()
		);
		$crmEntityRes = new CrmCreateResponse($res);
		$entity = $crmEntityRes->getResults();
		return $entity;
	}

	public function findEntity($entityName, $entityId) {
		$action = 'SGSingleRetreive';
		$params = [
			"entity"=>$entityName,
			"entity_id"=>$entityId
		];
		$request = $this->soapGenerator->generateSoap($action,$params);
        $res = $this->soapRequester->ExecuteSOAPRequest(
            $this->connector->getAuthHeader(),
            $request,
            $this->connector->getUrl()
        );
		$crmEntityRes = new CrmEntityResponse($res);
		$entity = $crmEntityRes->getResults();
		return $entity;
	}

    /**
     * This function returns all the entities from the CRM Dynamics.
     *
     * @param $entityName
     * @param null $paginationKey
     * @param array|null $fields
     * @param array|null $filter
     * @param array|null $order
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getAllEntityRecords($entityName, $paginationKey=null, array $fields=null, array $filter=null, array $order=null) {
        // We will start by loading the first page!
        $pageNumber = 1;
        $action = 'SGMultipleRetreive';
        $params = [
            "entity" => $entityName,
            "fields" => $fields,
            "criteria" => [
                'conditions' => $filter
            ],
            'totalCount' => 1000,
        ];
        // This is the flag used to check if we have more records after processing this page.
        $hasMoreRecords = true;
        // This stores all the entities returned by the entities
        $finalEntities = [];
        // This stores the cookie which is to be returned to Microsoft 360 Dynamics to render the next page.
        $pageCookie = null;
        // This is the result returned by the pagination result.
        $result = null;
        logger('Loading entity::' . $entityName);
        // Lets start the loop
        while ($hasMoreRecords) {
            try {
                logger("Loading Page number " . $pageNumber);
                // Lets set the page number we will be loading
                $params['pageNumber'] = $pageNumber;
                // Lets check if the last loop set any pageCookie which can be sent to load the next page.
                if(!is_null($pageCookie) && is_string($pageCookie)) {
                    $pageCookieXml = simplexml_load_string($pageCookie);
                    $params['pageCookie'] = '&lt;cookie page="' . (string)$pageCookieXml['page'] .
                        '"&gt;&lt;' . $paginationKey . ' last="' . (string)$pageCookieXml->{$paginationKey}['last'] .
                        '" first="' . (string)$pageCookieXml->{$paginationKey}['first'] . '" /&gt;&lt;/cookie&gt;';
                }
                // Shoot the request
                $request = $this->soapGenerator->generateSoap($action, $params);
                // Load th result
                $result = $this->soapRequester->ExecuteSOAPRequest($this->connector->getAuthHeader(), $request, $this->connector->getUrl());
                // Lets check if there was some result returned
                if(!empty($result)){
                    $crmEntityRes = new CrmMultipleEntityResponse($result);
                    if (is_array($finalEntities) && !empty($request['success']) && $request['success'] == false) {
                        // We have run into an error. Do not add this to your results.
                    } else {
                        $finalEntities = array_merge($finalEntities, $crmEntityRes->getResults());
                    }
                    // logger("Total entities loaded " . count($finalEntities));
                    $pageCookie = $crmEntityRes->getPagingCookie();
                    // Lets trigger all the entities are loaded!
                    $hasMoreRecords = ($crmEntityRes->hasMoreRecords() != "false" && $crmEntityRes->hasMoreRecords() != false);
                    // Increase the page number
                    $pageNumber++;
                } else {
                    // If no result was returned by default, just change the hasMoreRecords to false;
                    $hasMoreRecords = false;
                }
            } catch (\Exception $exception) {
                logger(print_r($pageCookie, true));
                logger(print_r($result, true));
                logger(print_r($params, true));
                logger(print_r($exception->getMessage(), true));
                logger($exception->getTraceAsString());
                throw $exception;
            }
        }
        return $finalEntities;
    }
}
