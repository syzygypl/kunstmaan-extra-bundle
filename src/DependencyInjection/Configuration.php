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

        $rootNode->children()->scalarNode('tinypng_api_key')->defaultNull();
        $rootNode->children()->scalarNode('generate_controller')->defaultNull();

        $assets = $rootNode->children()->arrayNode('assets');
        $assets->addDefaultsIfNotSet();
        $assets->children()->scalarNode('cdn_url')->defaultValue("");
        $assets->children()->scalarNode('web_prefix')->defaultNull();

        $redis = $rootNode->children()->arrayNode('redis');
        $redis->addDefaultsIfNotSet();
        $redis->children()->scalarNode('host')->defaultValue("localhost");
        $redis->children()->scalarNode('port')->defaultValue(6379);
        $redis->children()->scalarNode('password')->defaultNull();

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

        /** @var ArrayNodeDefinition $bem */
        $bem = $rootNode->children()->arrayNode('bem')->normalizeKeys(false);
        $bem = $bem->prototype('array');
        $bem->normalizeKeys(false)->prototype('scalar');

        /** @var ArrayNodeDefinition $imgix */
        $imgix = $rootNode->children()->arrayNode('imgix')->addDefaultsIfNotSet();
        $imgix->children()->scalarNode('bucket')->defaultNull();

        /** @var ArrayNodeDefinition $presets */
        $presets = $imgix->children()->arrayNode('presets')->prototype('array');
        $presets->prototype('scalar');

        $srcset = $rootNode->children()->arrayNode('srcset')->addDefaultsIfNotSet();
        $srcset->children()->scalarNode('default_filter')->defaultValue('srcset');
        $srcset->children()->integerNode('image_width_threshold')->defaultValue(100);
        $srcset->children()->arrayNode('breakpoints')->prototype('scalar');

        return $treeBuilder;

    }
}
