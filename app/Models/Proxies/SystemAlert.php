<?php
namespace App\Models\Proxies;

use App\Models\Proxies\QueueProxy;
use Util;

/**
 * Proxy class to create a system alert data holder
 */
class SystemAlert {
    /**
     * Scope of the error (portal,admin,api)
     * @var string
     */
    public $scope = "";

    /**
     * Impacted item (Node / Application / Service / System)
     * @var string
     */
    public $ci = "";

    /**
     * The name / identifier of the event
     * @var string
     */
    public $metric_name = "";

    /**
     * What is the event relating to / impacting
     * @var string
     */
    public $metric_type = "";

    /**
     * Severity of the event
     * @var string
     */
    public $severity = "";

    /**
     * Original event message
     * @var string
     */
    public $message = "";

    /**
     * Event created at
     * @var timestamp
     */
    public $created_at = null;

    /**
     * Actor id
     * @var null
     */
    public $actor_id = null;

    /**
     * Constructor
     */
    public function __construct() {
        $this->created_at = date('Y-m-d H:i:s');
    }

    /**
     * Function to hybernate the object
     * @param  array $data
     * @return void
     */
    public function populate($data) {
        $this->ci = array_get($data, 'ci', "Application");
        $this->metric_name = array_get($data, 'metric_name', "");
        $this->metric_type = array_get($data, 'metric_type', "AppEvent");
        $this->severity = array_get($data, 'severity', "Minor");
        $this->message = array_get($data, 'message', "");
        $this->actor_id = array_get($data, 'actor_id', null);
    }

    /**
     * Function to trigger email through logproxy
     * @return [type] [description]
     */
    public function trigger() {
        $placeholders = array(
            'ci' => $this->ci,
            'metric_name' => $this->metric_name,
            'metric_type' => $this->metric_type,
            'severity' => $this->severity,
            'message' => $this->message,
            'occurred_at' => $this->created_at,
        );

        if (!is_null($this->actor_id)) {
            $placeholders['actor_id'] = $this->actor_id;
        }

        // switch to send to admin or as system alert
        $attributes = array(
           "to" => \ConfigProxy::get('mail.to.default'),
           "cc" => \ConfigProxy::get('mail.cc.default'),
           );

        // add to queue
        QueueProxy::add('SendEmail', array(
            'context' => 'system-alert',
            'attributes' => $attributes, 
            'placeholders' => $placeholders,
        ));
    }
}
