<?php

namespace App\Models\Repositories;

use App\Models\Entities\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use App\Models\Gateways\Email\AWSEmail;
use App\Models\Gateways\Email\SesRawMailer;
use View;

class EmailRepository {

	public function sendUserActivationEmail(User $user) {
		$from = Config::get('ins_emails.user_activation.from');
		$to = $user->email;
        $message = View::make('site/email/user_activation', array(
            'user' => $user,
            'case_manager' => !empty($user->employee) && !empty($user->employee->caseManager) ? $user->employee->caseManager : null
        ))->render();
		$subject = 'User Activation E-Mail';
		$email_gateway = new AWSEmail();
        $email_gateway->send($to, $from, $subject, $message, array());
	}

    /**
     * The RD Sent is changed Mobility email only!
     *
     * @param User $user
     * @param null $employee
     */
    public function sendUserUploadRDEmail(User $user, $employee = null) {
        // From Mobility
        $from = Config::get('ins_emails.user_rd_upload.from');
        $to = Config::get('ins_emails.user_rd_upload.to');
        $message = View::make('site/email/user_rd_upload', array('user' => $user))->render();
        $subject = 'Candidate RD Upload';
        $email_gateway = new AWSEmail();
        $email_gateway->send($to, $from, $subject, $message, array());
    }

	public function send($message, $subject, $from, $to, $options = array()) {
        $email_gateway = new AWSEmail();
        return $email_gateway->send($to, $from, $subject, $message, $options);
    }
   
    public function sendSMTP($message, $subject, $from, $to, $options = array()) {
        Mail::send('site.email.test', array(), function ($m) use ($subject,$from,$to) {
            $m->from($from, 'From INS Mobility');
            $m->to($to, "ADIL")->subject($subject);
        });
    }

    public function sendSMTPAttachment($to, $from, $subject, $message, $html='', $attachments=array(), $returnPath='', $cc='', $bcc='') {
        $email_gateway = new SesRawMailer();
        $email_gateway->send($to, $subject, $message, $from, $html, $attachments, $returnPath, $cc, $bcc);
    }

	public function sendAttachment($to,  $from, $subject, $message, $html='', $attachments=array(), $returnPath='', $cc='', $bcc='') {
        $email_gateway = new SesRawMailer();
        $email_gateway->send($to, $subject, $message, $from, $html, $attachments, $returnPath, $cc, $bcc);
    }
}