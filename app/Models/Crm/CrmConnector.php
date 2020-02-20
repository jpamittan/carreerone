<?php
namespace App\Models\Crm;

class CrmConnector {
	private $authHeader;
	private $controller;
	private $url;
	function __construct($url, $username, $password) {
		echo "Crm connector constructor inialized...\n";
		$crmAuth = new CrmAuth();
		$this->url = $url;
		$this->authHeader = $crmAuth->GetHeaderOnline($username, $password, $this->url);
		$this->controller = new CrmController($this);
	}
	
	public function getAuthHeader(){
		return $this->authHeader;
	}

	public function getUrl(){
		return $this->url;
	}
	
	public function getController(){
		return $this->controller;
	}
}
