<?php

namespace App\Models\Services;

use App\Models\Crm\CrmConnector;
use Illuminate\Support\Facades\Config;
use App\Models\Repositories\UserPushRepository;

class UserPushService {
	private $userPushRepository;
	
	function __construct(UserPushRepository $userPushRepository) { 
		$this->userPushRepository = $userPushRepository;
	}

	public function processUserPush() {
		$this->userPushRepository->pushEmployees( );
	}
}
