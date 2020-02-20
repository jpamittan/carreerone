<?php

namespace App\Models\Crm\Response;

use App\Models\Crm\Response\CrmResponse;

class CrmEntityResponse extends CrmResponse {
	function getResults(){
		if(!$this->validateResponse()) {
			return ["success" => false];
		}
		return $this->generateResponse();
	}
	
	private function generateResponse(){
		$fields = [];
		$array = $this->multi_array_value('bAttributes',$this->respose_array);
		foreach ($array['bKeyValuePairOfstringanyType'] as $field){
			$fields[$field['ckey']] = $field['cvalue'];
		}
		return $fields;
	}
}
