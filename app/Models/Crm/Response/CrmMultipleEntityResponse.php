<?php
namespace App\Models\Crm\Response;

use App\Models\Crm\Response\CrmResponse;

class CrmMultipleEntityResponse extends CrmResponse {
    function getResults() {
		if (!$this->validateResponse()) {
			return ["success" => false];
        }
		return $this->generateResponse();
	}

	private function generateResponse() {
		$entities =[];
		$array = $this->multi_array_value('bEntity',$this->respose_array);
		if (isset($array['bAttributes'])) {
			$fields = [];
			foreach ($array['bAttributes']['bKeyValuePairOfstringanyType'] as $field) {
				$fields[$field['ckey']] = $field['cvalue'];
			}
			$entities[] = $fields;
		} else {
		    if (is_array($array)) {
                foreach ($array as $entity) {
                    $fields = [];
                    foreach ($entity['bAttributes']['bKeyValuePairOfstringanyType'] as $field) {
                        $fields[$field['ckey']] = $field['cvalue'];
                    }
                    $entities[] = $fields;
                }
            }
		}
		return $entities;
	}

    /**
     * @return bool|null
     */
    function getTotalRecordCount() {
        return $this->multi_array_value('bTotalRecordCount', $this->respose_array);
    }

    /**
     * @return bool|null
     */
    function getPagingCookie() {
        return $this->multi_array_value('bPagingCookie', $this->respose_array);
    }

    /**
     * @return bool|null
     */
    function hasMoreRecords()
    {
        return $this->multi_array_value('bMoreRecords', $this->respose_array);
    }
}
