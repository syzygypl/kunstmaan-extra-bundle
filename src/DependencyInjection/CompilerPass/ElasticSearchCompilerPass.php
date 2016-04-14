<?php

namespace ArsThanea\KunstmaanExtraBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ElasticSearchCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $id = 'kunstmaan_search.search_provider.elastica';
        if (false === $container->hasDefinition($id) || false === $container->hasParameter('elasticsearch.url')) {
            return ;
        }

        $definition = $container->getDefinition($id);

        $value = rtrim($container->getParameter('elasticsearch.url'), '/') . '/';
        $definition->addMethodCall('setNodes', [[['url' => $value]]]);
    }
}
