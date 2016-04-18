<?php


namespace ArsThanea\KunstmaanExtraBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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

        $search = $rootNode->children()->arrayNode('search');
        $search->addDefaultsIfNotSet();
        $search->children()->scalarNode('url')->defaultNull();
        $search->children()->scalarNode('replicas')->defaultValue(1);
        $search->children()->scalarNode('shards')->defaultValue(4);

        /** @var ArrayNodeDefinition $dateFormats */
        $dateFormats = $rootNode->children()->arrayNode('date_formats');
        $dateFormats->defaultValue([]);

        $dateFormats = $dateFormats->prototype('array');
        $dateFormats->prototype('scalar');



        return $treeBuilder;

    }
}
