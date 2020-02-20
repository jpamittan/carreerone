<?php
namespace Application\Models\Gateways;

class  Encrypt
{
        public static function encryptString($string){
                $key = 's8TFuu2K0QM4ohi7iltwUtz7UxiBtIyd';
                $iv = mcrypt_create_iv(
                    mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC),
                    MCRYPT_DEV_URANDOM
                );

                $encrypted = base64_encode(
                    $iv .
                    mcrypt_encrypt(
                        MCRYPT_RIJNDAEL_128,
                        hash('sha256', $key, true),
                        $string,
                        MCRYPT_MODE_CBC,
                        $iv
                    )
                );
                return $encrypted;
        }
        public static function decryptString($encrypted){
                $key = 's8TFuu2K0QM4ohi7iltwUtz7UxiBtIyd';
                $data = base64_decode($encrypted);
                $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));

                  $decrypted = rtrim(
                    mcrypt_decrypt(
                        MCRYPT_RIJNDAEL_128,
                        hash('sha256', $key, true),
                        substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)),
                        MCRYPT_MODE_CBC,
                        $iv
                    ),
                    "\0"
                );
                return $decrypted;
        }


        public static function encryptStringMonster($string){
                $key = 'Why$h0uldIT3llY0u';
                return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
                 
        }
        public static function decryptStringMonster($encrypted){
                $key = 'Why$h0uldIT3llY0u';
                return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($encrypted), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
                 
        }
        
}