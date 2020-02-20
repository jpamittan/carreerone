<?php
namespace App\Models\Gateways;

use App\Models\Proxies\FileProxy;

/**
 * Interface defining redact gateway
 */
interface RedactGateway
{
    /**
     * Function to clean uploaded resume using fileproxy
     * @param FileProxy $resume
     */
    public function clean(FileProxy $file);

    /**
     * Function to get the error of last executed command
     * @return mixed
     */
    public function getLastError();

    /**
     * Function to set meta data for the request
     * @return mixed|null
     */
    public function setMetaData(array $meta);
}
