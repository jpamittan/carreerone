<?php

namespace Application\Models\Gateways\Aws\Sns;

use Aws\Sns\SnsClient;

class SnsGateway
{
    protected $config = [
        'key' => '',
        'secret' => '',
        'region' => ''
    ];

    protected $client = null;

    public function __construct($config = [])
    {
        foreach ($this->config as $key => $value) {
            if (isset($config[$key])) {
                $this->config[$key] = $config[$key];
            }
        }
    }

    public function createMessage()
    {
        return new SnsMessage;
    }

    protected function getClient()
    { print_r($this->config);exit;
        if (is_null($this->client)) {
            $this->client = SnsClient::factory($this->config);
        }

        return $this->client;
    }

    public function publish(SnsMessage $message)
    {
        return $this->getClient()->publish([
            'TargetArn' => $message->getTopic(),
            'Message' => $message->getMessage(),
            'Subject' => $message->getSubject()
        ]);
    }
}