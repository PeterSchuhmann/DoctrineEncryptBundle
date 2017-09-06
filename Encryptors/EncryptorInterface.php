<?php

namespace Resomedia\DoctrineEncryptBundle\Encryptors;

/**
 * Encryptor interface for encryptors
 * @author Victor Melnik <melnikvictorl@gmail.com> (modified)
 */
interface EncryptorInterface {

    /**
     * Must accept secret key for encryption
     * @param string $secretKey the encryption key
     * @param string $protocol
     * @param string $iv
     */
    public function __construct($secretKey, $protocol, $iv);

    /**
     * @param string $data Plain text to encrypt
     * @return string Encrypted text
     */
    public function encrypt($data);

    /**
     * @param string $data Encrypted text
     * @return string Plain text
     */
    public function decrypt($data);
}