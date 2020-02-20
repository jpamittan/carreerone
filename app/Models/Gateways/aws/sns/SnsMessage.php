<?php

namespace Application\Models\Gateways\Aws\Sns;

class SnsMessage
{
    protected $message;
    protected $subject;
    protected $topic;
    protected $headers;

    public function setMessage($message)
    {
        if (!mb_check_encoding($message, 'UTF-8')) {
            $message = utf8_encode($message);
        }
        $this->message = $message;
        return $this;
    }

    public function setTopic($topic)
    {
        $this->topic = $topic;
        return $this;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getTopic()
    {
        return $this->topic;
    }

    public function getSubject()
    {
        return $this->subject;
    }
}