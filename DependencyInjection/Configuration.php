<?php

namespace Resomedia\DoctrineEncryptBundle\DependencyInjection;

use Resomedia\DoctrineEncryptBundle\Encryptors\Encryptor;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('resomedia_doctrine_encrypt');

        $rootNode
            ->children()
                ->scalarNode('protocol')
                    ->isRequired()
                ->end()
                ->scalarNode('iv')
                    ->isRequired()
                ->end()
                ->scalarNode('secret_key')
                ->end()
                ->scalarNode('encryptor_class')
                    ->defaultValue(Encryptor::class)
                ->end()
            ->end();

        return $treeBuilder;
    }
}
