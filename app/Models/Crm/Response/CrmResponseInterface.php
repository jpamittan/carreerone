<?php

namespace App\Models\Crm\SoapGenerator;

interface  CrmResponseInterface {
	public function parse($response);
}
