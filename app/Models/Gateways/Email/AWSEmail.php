<?php
namespace App\Models\Gateways\Email;

use App\Models\Gateways\EmailGateway;
use Aws\Ses\SesClient;
use ConfigProxy;
use Exception;
use Guzzle\Service\Exception\CommandTransferException;
use App\Models\Entities\Email;
use Mail;
use Illuminate\Support\Facades\Config;
/**
 * AWS SES Implementation of Email Gateway
 */
class AWSEmail implements EmailGateway {
    /**
     * Placeholder for agent
     * @var null
     */
    protected $agent = null;

    /**
     * Placeholder for queue
     * @var array
     */
    protected $queue = array();

    /**
     * Construct by creating an agent
     */
    public function __construct() {
        $this->agent = SesClient::factory([
            'credentials' => [
                'key'    => 'AKIAINBOC4DL2BCZVXOQ',
                'secret' => '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',
            ],
            'region' => 'us-east-1',
            'version' => 'latest',

            // You can override settings for specific services
            'Ses' => [
                'region' => 'us-east-1',
            ],
        ]);
    }

    /**
     * Implemented function to send email
     * @param string $to
     * @param string $from
     * @param string $subject
     * @param string $content
     * @param mixed $options
     * @return boolean
     */
    public function send($recipients, $from, $subject, $content, $options = array()) {
        if(!is_array($recipients)){
            $recipients = array($recipients);
        }
        $email = new Email();
        $email->to = implode(',',$recipients);
        $email->from = $from;
        $email->subject = $subject;
        $email->message = $content;
        $email->additional_details = !empty($options) ? json_encode($options) : '';
        $email->save();
        $env = app()->environment();
        if($env == 'local' || $env == 'preprod'){
            return array();
        }
        $attachments = [];
        // Lets check if we have any attachments in the Options
        if(!empty($options['attachments'])) {
            $attachments = $options['attachments'];
        }
        $from_name = Config::get('mail.from.name');
        $from_address = Config::get('mail.from.address');
        //SMTP
        if(true){
            $result = Mail::send(array(), array(), function ($message) use (
                $subject, $from, $from_name, $from_address,
                $recipients, $content, $attachments
            ) {
                $message->from($from_address, $from_name);
                $message->to($recipients)->subject($subject);
                $message->setBody($content, 'text/html');
                // Lets check if we have any attachments to add to this emails.
                if (!empty($attachments)) {
                    foreach ($attachments as $attachment) {
                        if (!empty($attachment) && file_exists($attachment)) {
                            $message->attach($attachment);
                        }
                    }
                }
            });
            return $result;
        } else {
            return array();    
        }//else
    }

    /**
     * Implemented function to queue email
     * @param string $to
     * @param string $from
     * @param string $subject
     * @param string $content
     * @param mixed $options
     * @return boolean
     */
    public function addToQueue($recipients, $from, $subject, $content, $options = array()) {
        if(!is_array($recipients)){
            $recipients = array($recipients);
        }
        $this->queue[] = $this->agent->getCommand("sendEmail", array(
            'Source' => $from,
            'Destination' => array(
                'ToAddresses' => $recipients,
            ),
            'Message' => array(
                'Subject' => array(
                    'Data' => $subject,
                ),
                'Body' => array(
                    'Text' => array(
                        'Data' => $content,
                    ),
                    'Html' => array(
                        'Data' => $content,
                    ),
                ),
            ),
        ));
        return true;
    }

    /**
     * Implemented function to process queue
     * @return boolean
     */
    public function processQueue() {
        $result = array(
            'success' => 0,
            'error' => 0,
        );
        try {
            $success = $this->agent->execute($this->queue);
            echo sizeof($this->queue);
            $result['success'] = count($success);
        } catch (CommandTransferException $e) {
            // log success
            $success = $e->getSuccessfulCommands();
            $result['success'] = count($success);
            // log count of errors
            $errors = $e->getFailedCommands();
            $result['errors'] = count($errors);
        } catch (Exception $e) {
            throw $exp;
        }
        $this->queue = array();
        return $result;
    }
}
