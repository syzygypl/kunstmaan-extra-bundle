<?php


namespace ArsThanea\KunstmaanExtraBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kunstmaan_extra');

        $assets = $rootNode->children()->arrayNode('assets');
        $assets->addDefaultsIfNotSet();
        $assets->children()->scalarNode('cdn_url')->defaultValue("");
        $assets->children()->scalarNode('web_prefix')->defaultNull();


        return $treeBuilder;

    }
}