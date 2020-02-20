<?php
namespace App\Models\Gateways;

interface EmailGateway
{
    /**
     * Function to send email
     * @param string $to
     * @param string $from
     * @param string $subject
     * @param string $content
     * @param mixed $options
     * @return boolean
     */
    public function send($to, $from, $subject, $content, $options = array());

    /**
     * Function to queue email
     * @param string $to
     * @param string $from
     * @param string $subject
     * @param string $content
     * @param mixed $options
     * @return boolean
     */
    public function addToQueue($to, $from, $subject, $content, $options = array());

    /**
     * Function to process queue
     * @return boolean
     */
    public function processQueue();
}
