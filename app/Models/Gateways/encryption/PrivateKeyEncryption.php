<?php
namespace Application\Models\Gateways\Encryption;

use Application\Models\Gateways\EncryptionGateway;

class PrivateKeyEncryption implements EncryptionGateway
{
    public function encrypt($data)
    {
    }
    public function decrypt($data)
    {
    }

    public function checkHash($text, $hash)
    {
    }
}
