<?php

namespace Vinorcola\DynamicVoterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('vinorcola_dynamic_voter');

        $rootNode
            ->children()
                ->scalarNode('cache')
                    ->defaultNull()
                ->end() // cache
                ->arrayNode('rules')
                    ->prototype('scalar')->end()
                ->end() // rules
            ->end();

        return $treeBuilder;
    }
}
