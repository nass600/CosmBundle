<?php

namespace Nass600\CosmBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;

/**
 * Nass600CosmExtension
 *
 * Class that defines the Dependency Injection Extension to expose the bundle's semantic configuration
 *
 * @package Nass600CosmBundle
 * @subpackage DependencyInjection
 * @author Ignacio Velázquez Gómez <ivelazquez85@gmail.com>
 */
class Nass600CosmExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {       
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        // registering services
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('managers.xml');
    }
    
    public function getAlias()
    {
        return 'nass600_cosm';
    }
}
