<?php

namespace Resomedia\DoctrineEncryptBundle\Encryptors;

/**
 * Class Encryptor
 * @package Resomedia\DoctrineEncryptBundle\Encryptors
 */
class Encryptor implements EncryptorInterface
{
    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var protocol
     */
    private $protocol;

    /**
     * @var vector
     */
    private $vector;


    /**
     * Must accept secret key for encryption
     * @param string $secretKey the encryption key
     * @param string $protocol
     * @param string $iv
     */
    public function __construct($secretKey, $protocol, $iv)
    {
        $this->secretKey = $secretKey;
        $this->protocol = $protocol;
        $this->vector = $iv;
    }

    /**
     * Add <ENC> Tag for detect encrypted value
     * @param string $data Plain text to encrypt
     *
     * @throws \Exception
     *
     * @return string Encrypted text
     */
    public function encrypt($data)
    {
        $value = base64_encode(openssl_encrypt($data, $this->protocol, $this->secretKey, 0, $this->vector));
        if ($value === false) {
            throw new \Exception('Impossible to crypt data');
        }
        return '[ENC]' . $value;
    }

    /**
     * remove <ENC> Tag before to decrypt data
     * @param string $data Encrypted text
     *
     * @throws \Exception
     *
     * @return string Plain text
     */
    public function decrypt($data)
    {
        $value = openssl_decrypt(base64_decode(str_replace('[ENC]', '',$data)), $this->protocol, $this->secretKey, 0, $this->vector);
        if ($value === false) {
            throw new \Exception('Impossible to decrypt data');
        }
        return $value;
    }
}