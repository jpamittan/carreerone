<?php

namespace App\Models\Crm\SoapGenerator;

class SGDelete implements SoapGeneratorInterface {
	public function getXML(array $params){
		$xml = "<s:Body>";
		$xml .= "<Execute xmlns=\"http://schemas.microsoft.com/xrm/2011/Contracts/Services\" xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\">";
		$xml .= "<request i:type=\"a:DeleteRequest\" xmlns:a=\"http://schemas.microsoft.com/xrm/2011/Contracts\">";
		$xml .= "<a:Parameters xmlns:b=\"http://schemas.datacontract.org/2004/07/System.Collections.Generic\">";
		$xml .= "<a:KeyValuePairOfstringanyType>";
		$xml .= "<b:key>Target</b:key>";
		$xml .= "<b:value i:type=\"a:EntityReference\">";
		$xml .= "<a:Id>".$params["entity_id"]."</a:Id>";
		$xml .= "<a:LogicalName>".$params["entity"]."</a:LogicalName>";
		$xml .= "<a:Name i:nil=\"true\" />";
		$xml .= "</b:value>";
		$xml .= "</a:KeyValuePairOfstringanyType>";
		$xml .= "</a:Parameters>";
		$xml .= "<a:RequestId i:nil=\"true\" />";
		$xml .= "<a:RequestName>Delete</a:RequestName>";
		$xml .= "</request>";
		$xml .= "</Execute>";
		$xml .= "</s:Body>";
		return $xml;
	}
}
