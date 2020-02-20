<?php
namespace App\Models\Crm\Response;

use App\Models\Crm\Response\CrmResponse;

class CrmCreateResponse extends CrmResponse {
	function getResults(){
		if(!$this->validateResponse()) {
			return ["success" => false];
		}
		return $this->generateResponse();
	}
	
	private function generateResponse(){
		$fields = [];
		$array = $this->multi_array_value('bResults',$this->respose_array);
		foreach ($array as $field){
			$fields[$field['ckey']] = $field['cvalue'];
		}
		return $fields;
	}
}
