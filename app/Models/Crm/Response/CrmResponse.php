<?php

namespace App\Models\Crm\Response;

abstract class CrmResponse {
	protected $respose_array;
	protected  $found;
	
	public abstract  function getResults();

	function __construct($response) {
		$this->processResponse($response);
	}
	
	public function processResponse($response) {
		ini_set('memory_limit','1024M');
		$response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
		$xml = new \SimpleXMLElement($response);
		$body = $xml->xpath('//sBody')[0];
		$this->respose_array = json_decode(json_encode((array)$body), TRUE);
	}
	
	public function validateResponse() {
		if($this->multi_array_key_exists('bResults',$this->respose_array)) {
			return true;
		} else {
			return false;	
		}
	}
	
	function multi_array_key_exists($key, $array) {
		if (array_key_exists($key, $array)) {
			return true;
		} else {
			foreach ($array as $nested) {
				if (is_array($nested) && $this->multi_array_key_exists($key, $nested)) {
					return true;
				}
			}
		}
		return false;
	}
	
	
	function multi_array_value($key, $array) {
		$this->found = null;
		if (array_key_exists($key, $array)) {
			$this->found =$array[$key] ;
			return true;
		} else {
			foreach ($array as $nested) {
				if (is_array($nested) &&  
					$this->multi_array_value($key, $nested) === true) {
					break;
				}
			}
		}
		return $this->found;
	}
}
