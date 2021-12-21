<?php


namespace TallmanCode\SettingsBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('tmc_settings');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->variableNode('default_settings')
                    ->info("Array of groups of settings with a child array of key and value")
                    ->cannotBeEmpty()
                ->end()
            ->end();
        return $treeBuilder;
    }
}