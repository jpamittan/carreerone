<?php
namespace App\Models\Proxies;

use Queue;

/**
 * Proxy class as a wrapper around Queue implementation
 */
class QueueProxy {
    protected static $queue_types = array(
        'email' => 'App\Commands\Console\SendEmail',
    );

    /**
     * Static proxy function to add items to queue
     * @param string $type
     * @return mixed $options
     */
    public static function add($type, $options) {
        $handler = array_get(self::$queue_types, $type, null);
        if ($handler) {
            Queue::push($handler, $options);
        }
    }
}
