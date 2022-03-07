<?php


namespace TallmanCode\SettingsBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class TmcSettingsExtension extends Extension
{

    /**
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);

        if(!$configuration){
            throw new InvalidArgumentException("Tmc Settings Bundle configuration is null.");
        }

        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('tmc_settings.setting_config');
        $definition->setArgument(0, $config['resources']);

        //TODO remove
        $definition = $container->getDefinition('tmc_settings.settings_manager');

        $definition->setArgument(5, $config['resources']);

        $definition = $container->getDefinition('tmc_settings.setting_defaults');

        $definition->setArgument(0, $config['resources']);
    }

    public function getAlias(): string
    {
        return 'tmc_settings';
    }
}