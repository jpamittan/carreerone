<?php
namespace Application\Models\Gateways\Encryption;

use Application\Models\Gateways\EncryptionGateway;

class OneWayEncryption implements EncryptionGateway
{
    /**
     * Use md5 to encrypt a password
     * @param $string
     * @return mixed|null
     */
    public function encrypt($data)
    {
        return password_hash($data, PASSWORD_BCRYPT, array('salt' => time() . "@3huwq23.afsdgvfgsfsd23432dsf"));
    }

    /**
     * One way encryption so nothing to do here - overriden as per interface contract
     * @param $data
     * @return mixed|null
     */
    public function decrypt($data)
    {}

    /**
     * Function that will check whether text is equal to the hashed value
     * @param string $text
     * @param string $hashed text
     * @return boolean
     */
    public function checkHash($text, $hash)
    {
      return password_verify($text, $hash);
    }
}
