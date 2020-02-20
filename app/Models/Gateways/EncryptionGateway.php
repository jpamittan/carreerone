<?php
namespace Application\Models\Gateways;

interface EncryptionGateway
{
    /**
     * Function to encrypt a string
     * @param $data
     * @return mixed|null
     */
    public function encrypt($data);

    /**
     * Function to decrypt a string
     * @param $data
     * @return mixed|null
     */
    public function decrypt($data);

    /**
     * Function that will check whether text is equal to the hashed value
     * @param string $text
     * @param string $hashed text
     * @return boolean
     */
    public function checkHash($text, $hash);
}
