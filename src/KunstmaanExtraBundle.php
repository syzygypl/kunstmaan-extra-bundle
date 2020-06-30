<?php

namespace ArsThanea\KunstmaanExtraBundle;

use ArsThanea\KunstmaanExtraBundle\DependencyInjection\CompilerPass\ElasticSearchCompilerPass;
use ArsThanea\KunstmaanExtraBundle\DependencyInjection\CompilerPass\ImagineChainedDataLoaderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanExtraBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ElasticSearchCompilerPass());
        $container->addCompilerPass(new ImagineChainedDataLoaderCompilerPass());
    }
}
