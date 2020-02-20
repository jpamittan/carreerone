<?php
namespace Application\Models\Gateways\Encryption;

use Crypt;
use Application\Models\Gateways\EncryptionGateway;

class NoEncryption implements EncryptionGateway
{
    /**
     * No Encryption Needed
     * @param $data
     * @return mixed|null
     */
    public function encrypt($data)
    {
        return $data;
    }

    /**
     * No Encryption Needed
     * @param $data
     * @return mixed|null
     */
    public function decrypt($data)
    {
        return $data;
    }

    /**
     * No Encryption so just compare values
     * @param string $text
     * @param string $hashed text
     * @return boolean
     */
    public function checkHash($text, $hash)
    {
      return ($text === $hash);
    }
}
