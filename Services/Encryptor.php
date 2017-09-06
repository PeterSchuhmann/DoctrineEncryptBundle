<?php

namespace Resomedia\DoctrineEncryptBundle\Services;

/**
 * Class Encryptor
 * @package Resomedia\DoctrineEncryptBundle\Services
 */
class Encryptor
{
    /**
     * @var \Resomedia\DoctrineEncryptBundle\Encryptors\EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $iv;

    /**
     * @var string
     */
    private $protocol;

    /**
     * Encryptor constructor.
     * @param $encryptName
     * @param $key
     * @param $protocol
     * @param $iv
     */
    public function __construct($encryptName, $key, $protocol, $iv)
    {
        $this->iv = $iv;
        $this->key = $key;
        $this->protocol = $protocol;
        $reflectionClass = new \ReflectionClass($encryptName);
        $this->encryptor = $reflectionClass->newInstanceArgs(array(
            $key,
            $protocol,
            $iv
        ));
    }

    /**
     * @return object|\Resomedia\DoctrineEncryptBundle\Encryptors\EncryptorInterface
     */
    public function getEncryptor() {
        return $this->encryptor;
    }

    /**
     * @param \Resomedia\DoctrineEncryptBundle\Encryptors\EncryptorInterface $encryptName
     */
    public function setEncryptor($encryptName) {
        $reflectionClass = new \ReflectionClass($encryptName);
        $this->encryptor = $reflectionClass->newInstanceArgs(array(
            $this->key,
            $this->iv,
            $this->protocol
        ));
    }

    /**
     * @param $string
     * @return string
     */
    public function decrypt($string) {
        return $this->encryptor->decrypt($string);
    }

    /**
     * @param $string
     * @return string
     */
    public function encrypt($string) {
        return $this->encryptor->encrypt($string);
    }
}