<?php

namespace App\Models\Factories;

class ExternalFileFactory {
    private static $files = array(
        'S3' => 'App\Models\Gateways\File\S3File',
        'Test' => 'App\Models\Gateways\File\TestFile',
        'Local' => 'App\Models\Gateways\File\LocalFile',
        'SFTP' => 'App\Models\Gateways\File\SFTPFile',
        'FTP' => 'App\Models\Gateways\File\FTPFile',
        'default' => 'App\Models\Gateways\File\LocalFile',
    );

    public static function create($type) {
        if (is_null($type)) {
            $type = 'default';
        }
        $class = static::$files[$type];
        $gateway = new $class;
        return $gateway;
    }

    public static function types() {
        return array_keys(static::$files);
    }
}
