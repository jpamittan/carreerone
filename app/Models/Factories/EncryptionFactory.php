<?php

namespace Application\Models\Factories;

use Application\Models\Repositories\AWSConfigRepository;

class EncryptionFactory {
    private static $encryptions = array(
        'KMS' => 'Application\Models\Gateways\Encryption\KMSEncryption',
        'PrivateKey' => 'Application\Models\Gateways\Encryption\PrivateKeyEncryption',
        'Laravel' => 'Application\Models\Gateways\Encryption\LaravelEncryption',
        'NoEncryption' => 'Application\Models\Gateways\Encryption\NoEncryption',
        'Oneway' => 'Application\Models\Gateways\Encryption\OneWayEncryption',
        'default' => 'Application\Models\Gateways\Encryption\NoEncryption'
    );

    public static function create($type) {
        if (is_null($type)) {
            $type = 'default';
        }
        $class = static::$encryptions[$type];
        $encryption = new $class;
        return $encryption;
    }

    public static function types() {
        return array_keys(static::$encryptions);
    }
}
