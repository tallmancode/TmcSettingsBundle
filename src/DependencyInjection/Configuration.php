<?php


namespace TallmanCode\SettingsBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('tmc_settings');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->ArrayNode('resources')
                    ->info("Array of groups of settings with a child array of key and value")
                    ->arrayPrototype()
                        ->children()
                            ->arrayNode('defaults')
                                ->arrayPrototype()
                                    ->children()
                                        ->scalarNode('cast')
                                            ->defaultNull()
                                        ->end()
                                        ->variableNode('value')
                                            ->defaultNull()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}