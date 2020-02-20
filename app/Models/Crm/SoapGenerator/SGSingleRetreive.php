<?php 

namespace App\Models\Crm\SoapGenerator;

class SGSingleRetreive implements SoapGeneratorInterface {
	public function getXML(array $params){
		$xml = "<s:Body>";
		$xml .= "<Execute xmlns=\"http://schemas.microsoft.com/xrm/2011/Contracts/Services\" xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\">";
		$xml .= "<request i:type=\"a:RetrieveRequest\" xmlns:a=\"http://schemas.microsoft.com/xrm/2011/Contracts\">";
		$xml .= "<a:Parameters xmlns:b=\"http://schemas.datacontract.org/2004/07/System.Collections.Generic\">";
		$xml .= "<a:KeyValuePairOfstringanyType>";
		$xml .= "<b:key>Target</b:key>";
		$xml .= "<b:value i:type=\"a:EntityReference\">";
		$xml .= "<a:Id>".$params["entity_id"]."</a:Id>";
		$xml .= "<a:LogicalName>".$params["entity"]."</a:LogicalName>";
		$xml .= "<a:Name i:nil=\"true\" />";
		$xml .= "</b:value>";
		$xml .= "</a:KeyValuePairOfstringanyType>";
		$xml .= "<a:KeyValuePairOfstringanyType>";
		$xml .= "<b:key>ColumnSet</b:key>";
		$xml .= "<b:value i:type=\"a:ColumnSet\">";
		if(isset($params['fields']) && count($params['fields']) > 0){
			$xml .= '<a:AllColumns>false</a:AllColumns>';
			$xml .= '<a:Columns xmlns:c="http://schemas.microsoft.com/2003/10/Serialization/Arrays">';
			foreach ($params["fields"] as $field){
				$xml .= '<c:string>ins_firstname</c:string>';
			}
			$xml .= '</a:Columns>';
		} else {
			$xml .= '<a:AllColumns>true</a:AllColumns>';
			$xml .= '<a:Columns xmlns:c="http://schemas.microsoft.com/2003/10/Serialization/Arrays" />';
		}
		$xml .= "</b:value>";
		$xml .= "</a:KeyValuePairOfstringanyType>";
		$xml .= "</a:Parameters>";
		$xml .= "<a:RequestId i:nil=\"true\" />";
		$xml .= "<a:RequestName>Retrieve</a:RequestName>";
		$xml .= "</request>";
		$xml .= "</Execute>";
		$xml .= "</s:Body>";
		return $xml;
	}
}
