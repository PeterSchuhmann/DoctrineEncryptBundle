<?php

namespace Resomedia\DoctrineEncryptBundle\DependencyInjection;

use Resomedia\DoctrineEncryptBundle\Encryptors\Encryptor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class ResomediaDoctrineEncryptExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        //create config
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        //Set orm-service in array of services
        $services = array('orm' => 'orm-services');

        //If no secret key is set, check for framework secret, otherwise throw exception
        if (empty($config['secret_key'])) {
            if ($container->hasParameter('secret')) {
                $config['secret_key'] = $container->getParameter('secret');
            } else {
                throw new \RuntimeException('You must provide "secret_key" for DoctrineEncryptBundle or "secret" for framework');
            }
        }

        //If empty protocol
        if(empty($config['protocol'])) {
            throw new \RuntimeException('You must provide "protocol" for DoctrineEncryptBundle');
        }

        //If empty protocol
        if(empty($config['iv']) || strlen($config['iv']) != openssl_cipher_iv_length($config['protocol'])) {
            throw new \RuntimeException('You must provide "iv" for DoctrineEncryptBundle with size of ' . openssl_cipher_iv_length($config['protocol']) . ' for ' . $config['protocol']);
        }

        //If empty encryptor class
        if(empty($config['encryptor_class'])) {
            $config['encryptor_class'] = Encryptor::class;
        }

        $container->setParameter('resomedia_doctrine_encrypt.secret_key', $config['secret_key']);
        $container->setParameter('resomedia_doctrine_encrypt.protocol', $config['protocol']);
        $container->setParameter('resomedia_doctrine_encrypt.encryptor_class', $config['encryptor_class']);
        $container->setParameter('resomedia_doctrine_encrypt.iv', $config['iv']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load(sprintf('%s.yml', $services['orm']));
    }

    /**
     * Get alias for configuration
     *
     * @return string
     */
    public function getAlias() {
        return 'resomedia_doctrine_encrypt';
    }
}
