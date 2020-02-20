<?php

namespace App\Models\Crm\SoapGenerator;

class SGMultipleRetreive implements SoapGeneratorInterface {
	public function getXML(array $params) {
		$xml = '<s:Body>';
		$xml .= '<Execute xmlns="http://schemas.microsoft.com/xrm/2011/Contracts/Services" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';
		$xml .= '<request i:type="a:RetrieveMultipleRequest" xmlns:a="http://schemas.microsoft.com/xrm/2011/Contracts">';
		$xml .= '<a:Parameters xmlns:b="http://schemas.datacontract.org/2004/07/System.Collections.Generic">';
		$xml .= '<a:KeyValuePairOfstringanyType>';
		$xml .= '<b:key>Query</b:key>';
		$xml .= '<b:value i:type="a:QueryExpression">';
		$xml .= '<a:ColumnSet>';
		// Pagination parameters
        $totalCount = !empty($params['totalCount']) ? intval($params['totalCount']) : 0;
        $pageNumber = !empty($params['pageNumber']) ? intval($params['pageNumber']) : 1;
        $pageCookie = !empty($params['pageCookie']) ? $params['pageCookie'] : null;
		if(isset($params['fields']) && count($params['fields']) > 0) {
			$xml .= '<a:AllColumns>false</a:AllColumns>';
			$xml .= '<a:Columns xmlns:c="http://schemas.microsoft.com/2003/10/Serialization/Arrays">';
			foreach ($params["fields"] as $field) {
				$xml .= '<c:string>'.$field.'</c:string>';
			}
			$xml .= '</a:Columns>';
		} else {
			$xml .= '<a:AllColumns>true</a:AllColumns>';
		}
		$xml .= '</a:ColumnSet>';
		if(isset($params['criteria']) && count($params['criteria']['conditions']) > 0){
			$xml .= '<a:Criteria>';
			$xml .= '<a:Conditions>';
			foreach ($params['criteria']['conditions'] as $condition){
				$xml .= '<a:ConditionExpression>';
				$xml .= '<a:AttributeName>'.$condition["field"].'</a:AttributeName>';
				$xml .= '<a:Operator>'.$condition["operator"].'</a:Operator>';
				$xml .= '<a:Values xmlns:c="http://schemas.microsoft.com/2003/10/Serialization/Arrays">';
				$xml .= '<c:anyType i:type="d:'.$condition["value_type"].'" xmlns:d="http://www.w3.org/2001/XMLSchema">'.$condition["value"].'</c:anyType>';
				$xml .= '</a:Values>';
				$xml .= '</a:ConditionExpression>';	
			}
			$xml .= '</a:Conditions>';
			$xml .= '<a:FilterOperator>And</a:FilterOperator>';
			$xml .= '<a:Filters />';
			$xml .= '</a:Criteria>';
		}
		$xml .= '<a:Distinct>false</a:Distinct>';
		$xml .= '<a:EntityName>'.$params["entity"].'</a:EntityName>';
		$xml .= '<a:LinkEntities />';
		$xml .= '<a:Orders />';
		$xml .= '<a:PageInfo>';
		$xml .= '<a:Count>'.$totalCount.'</a:Count>';
		$xml .= '<a:PageNumber>'.$pageNumber.'</a:PageNumber>';
		if(is_null($pageCookie)) {
            $xml .= '<a:PagingCookie i:nil="true" />';
        } else {
		    $xml .= "<a:PagingCookie>{$pageCookie}</a:PagingCookie>";
        }
		$xml .= '<a:ReturnTotalRecordCount>true</a:ReturnTotalRecordCount>';
		$xml .= '</a:PageInfo>';
		$xml .= '<a:NoLock>false</a:NoLock>';
		$xml .= '</b:value>';
		$xml .= '</a:KeyValuePairOfstringanyType>';
		$xml .= '</a:Parameters>';
		$xml .= '<a:RequestId i:nil="true" />';
		$xml .= '<a:RequestName>RetrieveMultiple</a:RequestName>';
		$xml .= '</request>';
		$xml .= '</Execute>';
		$xml .= '</s:Body>';
		return $xml;
	}
}
