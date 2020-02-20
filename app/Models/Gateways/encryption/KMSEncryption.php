<?php
namespace Application\Models\Gateways\Encryption;

use Application\Models\Gateways\EncryptionGateway;
use Aws\Kms\KmsClient;
use ConfigProxy;

class KMSEncryption implements EncryptionGateway
{
    private $kms_client;

    public function __construct()
    {
        $this->kms_client = KmsClient::factory(ConfigProxy::get('aws'));
    }

    /**
     * Use KMS to encrypt a string
     * @param $string
     * @return mixed|null
     */
    public function encrypt($data)
    {
        $r = $this->kms_client->encrypt(array(
            "KeyId" => ConfigProxy::get('aws.kmskey'),
            "Plaintext" => $data,
        ));
        return $r->get("CiphertextBlob");
    }

    /**
     * Use MKS to decrypt a string
     * @param $data
     * @return mixed|null
     */
    public function decrypt($data)
    {
        $r = $this->kms_client->decrypt(array(
            "CiphertextBlob" => $data,
        ));
        return $r->get("Plaintext");
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
