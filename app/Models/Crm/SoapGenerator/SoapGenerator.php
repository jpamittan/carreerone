<?php

namespace App\Models\Crm\SoapGenerator;

class SoapGenerator {
	public function generateSoap($action, array $params){
		$soapGenerator = $this->getSoapGenerator($action);
		return $soapGenerator->getXml($params);
	}
	
	public function getSoapGenerator($action){
		$soapGenerator;
		switch ($action){
			case "Create":
				$soapGenerator = new SGCreate(); break;
			case "Update":
				$soapGenerator = new SGUpdate(); break;
			case "Delete":
				$soapGenerator = new SGDelete(); break;
			case "SGSingleRetreive":
				$soapGenerator = new SGSingleRetreive(); break;
			case "SGMultipleRetreive":
				$soapGenerator = new SGMultipleRetreive(); break;
			default: 
				$soapGenerator = null;
		}
		return $soapGenerator;
	}
}