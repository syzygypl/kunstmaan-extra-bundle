<?php

namespace ArsThanea\KunstmaanExtraBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ImagineChainedDataLoaderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $imgixLoader = 'ars_thanea.kunstmaan_extra.imagine.imgix_data_loader';
        $remoteLoader = 'ars_thanea.remote_media.imagine.chained_data_loader';

        if ($container->has($remoteLoader) && $container->has($imgixLoader)) {
            $definition = $container->getDefinition($remoteLoader);
            $definition->setMethodCalls(array_merge([
                ['addLoader', new Reference($imgixLoader)]
            ], $definition->getMethodCalls()));
        }
    }
}
