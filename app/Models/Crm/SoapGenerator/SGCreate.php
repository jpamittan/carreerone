<?php

namespace App\Models\Crm\SoapGenerator;

class SGCreate implements SoapGeneratorInterface {
	public function getXML(array $params) {
		$xml = "  <s:Body>";
		$xml .= " <Execute xmlns=\"http://schemas.microsoft.com/xrm/2011/Contracts/Services\" xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\">";
		$xml .= " <request i:type=\"a:CreateRequest\" xmlns:a=\"http://schemas.microsoft.com/xrm/2011/Contracts\">";
		$xml .= " <a:Parameters xmlns:b=\"http://schemas.datacontract.org/2004/07/System.Collections.Generic\">";
		$xml .= " <a:KeyValuePairOfstringanyType>";
		$xml .= " <b:key>Target</b:key>";
		$xml .= " <b:value i:type=\"a:Entity\">";
		$xml .= " <a:Attributes>";
		foreach($params["fields"] as $field){
			if($field["type"] == 'entity') {
				$xml .= " <a:KeyValuePairOfstringanyType>";
				$xml .= "  <b:key>".$field["name"]."</b:key>";
				$xml .= "  <b:value i:type=\"a:EntityReference\">";
				$xml .= "   <a:Id>".$field["value"]."</a:Id>";
				$xml .= "   <a:LogicalName>".$field["entity_name"]."</a:LogicalName>";
				$xml .= "   <a:Name i:nil=\"true\">";
				$xml .= "   </a:Name></b:value>";
				$xml .= " </a:KeyValuePairOfstringanyType>	";
			} elseif($field["type"] == 'option') {
				$xml .= " <a:KeyValuePairOfstringanyType>";
				$xml .= " <b:key>".$field["name"]."</b:key>";
				$xml .= " <b:value i:type=\"a:OptionSetValue\">";
				$xml .= " <a:Value>".$field["value"]."</a:Value>";
				$xml .= " </b:value>";
				$xml .= " </a:KeyValuePairOfstringanyType>";
			} else {
				$xml .= " <a:KeyValuePairOfstringanyType>";
				$xml .= " <b:key>".$field["name"]."</b:key>";
				$xml .= " <b:value i:type=\"c:".$field["type"]."\" xmlns:c=\"http://www.w3.org/2001/XMLSchema\">".$field["value"]."</b:value>";
				$xml .= " </a:KeyValuePairOfstringanyType>";
			}
		}
		$xml .= " </a:Attributes>";
		$xml .= " <a:EntityState i:nil=\"true\" />";
		$xml .= " <a:FormattedValues />";
		$xml .= " <a:Id>00000000-0000-0000-0000-000000000000</a:Id>";
		$xml .= " <a:LogicalName>".$params["entity"]."</a:LogicalName>";
		$xml .= " <a:RelatedEntities />";
		$xml .= " </b:value>";
		$xml .= " </a:KeyValuePairOfstringanyType>";
		$xml .= " </a:Parameters>";
		$xml .= " <a:RequestId i:nil=\"true\" />";
		$xml .= " <a:RequestName>Create</a:RequestName>";
		$xml .= " </request>";
		$xml .= " </Execute>";
		$xml .= " </s:Body>";
		return $xml;
	}
}
