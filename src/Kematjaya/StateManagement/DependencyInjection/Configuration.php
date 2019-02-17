<?php

/**
 * Description of Configuration
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */

namespace Kematjaya\StateManagement\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {
    
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kematjaya');
        
        $rootNode
            ->children()
                ->arrayNode('model')
                    ->children()
                        ->scalarNode('KmjState')->isRequired()->end()
                        ->scalarNode('KmjLink')->isRequired()->end()
                        ->scalarNode('KmjStateAction')->isRequired()->end()
                        ->scalarNode('KmjStateLink')->isRequired()->end()
                        ->scalarNode('KmjStateLog')->isRequired()->end()
                    ->end()
                ->end()
            ->end();
        
        return $treeBuilder;
    }
}
