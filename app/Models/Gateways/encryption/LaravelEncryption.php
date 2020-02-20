<?php
namespace Application\Models\Gateways\Encryption;

use Application\Models\Gateways\EncryptionGateway;
use Crypt;

class LaravelEncryption implements EncryptionGateway
{
    /**
     * Use Crypt to encrypt a string
     * @param $data
     * @return mixed|null
     */
    public function encrypt($data)
    {
        return Crypt::encrypt($data);
    }

    /**
     * Use Crypt to decrypt a string
     * @param $data
     * @return mixed|null
     */
    public function decrypt($data)
    {
        return Crypt::decrypt($data);
    }

    /**
     * Function that will check whether text is equal to the hashed value
     * @param string $text
     * @param string $hashed text
     * @return boolean
     */
    public function checkHash($text, $hash)
    {
    }
}
