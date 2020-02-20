<?php
namespace App\Models\Crm;

use Illuminate\Support\Facades\Config;
use App\Models\Crm\SoapGenerator\SoapGenerator;

class CrmService {
	private $authHeader;
	private $url;
	private $soapGenerator;
	function __construct() {
		$this->url = Config::get('crm.url');
		$this->authHeader  = $this->authCrm();
		$this->soapGenerator = new SoapGenerator();
	}
	
	private function authCrm() {
		$username = Config::get('crm.username');
		$password = Config::get('crm.password');
		$crmAuth = new CrmAuth ();
		$authHeader = $crmAuth->GetHeaderOnline($username, $password, $url);
		return $authHeader;
	}
	
	public function getAllEntityRecords($entityName, array $filter = null, array $order) {
		$this->soapGenerator->generateSoap('');
	}
	
	public function createEntity($entityName, array $fields) {
		$xml = "  <s:Body>";
		$xml .=  " <Execute xmlns=\"http://schemas.microsoft.com/xrm/2011/Contracts/Services\" xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\">";
		$xml .=  " <request i:type=\"a:CreateRequest\" xmlns:a=\"http://schemas.microsoft.com/xrm/2011/Contracts\">";
		$xml .=  " <a:Parameters xmlns:b=\"http://schemas.datacontract.org/2004/07/System.Collections.Generic\">";
		$xml .=  " <a:KeyValuePairOfstringanyType>";
		$xml .=  " <b:key>Target</b:key>";
		$xml .=  " <b:value i:type=\"a:Entity\">";
		$xml .=  " <a:Attributes>";
		$xml .=  " <a:KeyValuePairOfstringanyType>";
		$xml .=  " <b:key>ins_firstname</b:key>";
		$xml .=  " <b:value i:type=\"c:string\" xmlns:c=\"http://www.w3.org/2001/XMLSchema\">Test FirstName</b:value>";
		$xml .=  " </a:KeyValuePairOfstringanyType>";
		$xml .=  " <a:KeyValuePairOfstringanyType>";
		$xml .=  " <b:key>ins_surname</b:key>";
		$xml .=  " <b:value i:type=\"c:string\" xmlns:c=\"http://www.w3.org/2001/XMLSchema\">Test Surname</b:value>";
		$xml .=  " </a:KeyValuePairOfstringanyType>";
		$xml .=  " <a:KeyValuePairOfstringanyType>";
		$xml .=  " <b:key>ins_name</b:key>";
		$xml .=  " <b:value i:type=\"c:string\" xmlns:c=\"http://www.w3.org/2001/XMLSchema\">Test Name</b:value>";
		$xml .=  " </a:KeyValuePairOfstringanyType>";
		$xml .=  " </a:Attributes>";
		$xml .=  " <a:EntityState i:nil=\"true\" />";
		$xml .=  " <a:FormattedValues />";
		$xml .=  " <a:Id>00000000-0000-0000-0000-000000000000</a:Id>";
		$xml .=  " <a:LogicalName>ins_client</a:LogicalName>";
		$xml .=  " <a:RelatedEntities />";
		$xml .=  " </b:value>";
		$xml .=  " </a:KeyValuePairOfstringanyType>";
		$xml .=  " </a:Parameters>";
		$xml .=  " <a:RequestId i:nil=\"true\" />";
		$xml .=  " <a:RequestName>Create</a:RequestName>";
		$xml .=  " </request>";
		$xml .=  " </Execute>";
		$xml .=  " </s:Body>";
	}
	
	public function updateEntity($entityName, array $fields) {
		$xml = "  <s:Body>";
		$xml .=  " <Execute xmlns=\"http://schemas.microsoft.com/xrm/2011/Contracts/Services\" xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\">";
		$xml .=  " <request i:type=\"a:CreateRequest\" xmlns:a=\"http://schemas.microsoft.com/xrm/2011/Contracts\">";
		$xml .=  " <a:Parameters xmlns:b=\"http://schemas.datacontract.org/2004/07/System.Collections.Generic\">";
		$xml .=  " <a:KeyValuePairOfstringanyType>";
		$xml .=  " <b:key>Target</b:key>";
		$xml .=  " <b:value i:type=\"a:Entity\">";
		$xml .=  " <a:Attributes>";
		$xml .=  " <a:KeyValuePairOfstringanyType>";
		$xml .=  " <b:key>ins_firstname</b:key>";
		$xml .=  " <b:value i:type=\"c:string\" xmlns:c=\"http://www.w3.org/2001/XMLSchema\">Update FirstName</b:value>";
		$xml .=  " </a:KeyValuePairOfstringanyType>";
		$xml .=  " <a:KeyValuePairOfstringanyType>";
		$xml .=  " <b:key>ins_surname</b:key>";
		$xml .=  " <b:value i:type=\"c:string\" xmlns:c=\"http://www.w3.org/2001/XMLSchema\">Update Surname</b:value>";
		$xml .=  " </a:KeyValuePairOfstringanyType>";
		$xml .=  " <a:KeyValuePairOfstringanyType>";
		$xml .=  " <b:key>ins_name</b:key>";
		$xml .=  " <b:value i:type=\"c:string\" xmlns:c=\"http://www.w3.org/2001/XMLSchema\">Update Name</b:value>";
		$xml .=  " </a:KeyValuePairOfstringanyType>";
		$xml .=  " </a:Attributes>";
		$xml .=  " <a:EntityState i:nil=\"true\" />";
		$xml .=  " <a:FormattedValues />";
		$xml .=  " <a:Id>2cf26ded-c57f-e611-80eb-1458d05a586c</a:Id>";
		$xml .=  " <a:LogicalName>ins_client</a:LogicalName>";
		$xml .=  " <a:RelatedEntities />";
		$xml .=  " </b:value>";
		$xml .=  " </a:KeyValuePairOfstringanyType>";
		$xml .=  " </a:Parameters>";
		$xml .=  " <a:RequestId i:nil=\"true\" />";
		$xml .=  " <a:RequestName>Update</a:RequestName>";
		$xml .=  " </request>";
		$xml .=  " </Execute>";
		$xml .=  " </s:Body>";
	}
	
	public function findEntity($entityName, $entityId) {
		$xml = "<s:Body>";
		$xml .= "<Execute xmlns=\"http://schemas.microsoft.com/xrm/2011/Contracts/Services\" xmlns:i=\"http://www.w3.org/2001/XMLSchema-instance\">";
		$xml .= "<request i:type=\"a:RetrieveRequest\" xmlns:a=\"http://schemas.microsoft.com/xrm/2011/Contracts\">";
		$xml .= "<a:Parameters xmlns:b=\"http://schemas.datacontract.org/2004/07/System.Collections.Generic\">";
		$xml .= "<a:KeyValuePairOfstringanyType>";
		$xml .= "<b:key>Target</b:key>";
		$xml .= "<b:value i:type=\"a:EntityReference\">";
		$xml .= "<a:Id>d35dd282-5b75-e611-80ea-c4346bc5c378</a:Id>";
		$xml .= "<a:LogicalName>account</a:LogicalName>";
		$xml .= "<a:Name i:nil=\"true\" />";
		$xml .= "</b:value>";
		$xml .= "</a:KeyValuePairOfstringanyType>";
		$xml .= "<a:KeyValuePairOfstringanyType>";
		$xml .= "<b:key>ColumnSet</b:key>";
		$xml .= "<b:value i:type=\"a:ColumnSet\">";
		$xml .= "<a:AllColumns>false</a:AllColumns>";
		$xml .= "<a:Columns xmlns:c=\"http://schemas.microsoft.com/2003/10/Serialization/Arrays\">";
		$xml .= "<c:string>name</c:string>";
		$xml .= "</a:Columns>";
		$xml .= "</b:value>";
		$xml .= "</a:KeyValuePairOfstringanyType>";
		$xml .= "</a:Parameters>";
		$xml .= "<a:RequestId i:nil=\"true\" />";
		$xml .= "<a:RequestName>Retrieve</a:RequestName>";
		$xml .= "</request>";
		$xml .= "</Execute>";
		$xml .= "</s:Body>";
	}
	
	private function callSoap($xml) {
		$executeSoap = new CrmExecuteSoap();
		$response = $executeSoap->ExecuteSOAPRequest($this->authHeader, $xml, $this->url);
		$responsedom = new DomDocument ();
		$responsedom->loadXML($response);
	}
}
