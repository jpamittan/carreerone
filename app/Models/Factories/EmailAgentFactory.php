<?php

namespace App\Models\Factories;

class EmailAgentFactory {
    private static $agents = array(
        'AWS' => 'Application\Models\Gateways\Email\AWSEmail',
        'Test' => 'Application\Models\Gateways\Email\TestEmail',
    );

    public static function create($type) {
        if (is_null($type)) {
            $type = 'Test';
        }
        $class = static::$agents[$type];
        $agent = new $class;
        return $agent;
    }

    public static function types() {
        return array_keys(static::$agents);
    }
}
