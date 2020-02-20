<?php

namespace App\Models\Services;

use App\Models\Repositories\EmailRepository;

/**
 * Class to get any data requirements for email sending
 */
class EmailService {
	private $emailRepository;
	
	function __construct(EmailRepository $emailRepository) {
		$this->emailRepository = $emailRepository;
	}
	
	public function sendUserActivationEmail($user) {
		$this->emailRepository->sendUserActivationEmail($user);
	}
 
	public function sendUserUploadRDEmail($user, $employee= null) {
		$this->emailRepository->sendUserUploadRDEmail($user , $employee);
	}

    /**
     * @param $message
     * @param $subject
     * @param $from
     * @param $to
     * @param array $options
     *
     * @return bool
     */
    public function send($message, $subject, $from, $to, $options = array()) {
        return $this->emailRepository->send($message, $subject, $from, $to, $options);
    }
}
