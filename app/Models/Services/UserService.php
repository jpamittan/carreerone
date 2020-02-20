<?php

namespace App\Models\Services;
use App\Models\Repositories\UserRepository;

class UserService {
	private $userRepository;
	
	function __construct(UserRepository $userRepository) {
		$this->userRepository = $userRepository;
	}
	
	function verifyActivationToken($token) {
		return $this->userRepository->verifyActivationToken($token);
	}
	
	function activateAccount($data) {
		return $this->userRepository->activateAccount($data);
	}
	
	function getAllUsers() {
		return $this->userRepository->getAll();
	}

	function getUserDetails($userID) {
		return $this->userRepository->getUserDetails($userID);
	}

	function isActive($activeLinkLabel, $userID) {
		return $this->userRepository->isActiveUser($activeLinkLabel, $userID);
	}
}
