<?php

namespace ArsThanea\KunstmaanExtraBundle;

use ArsThanea\KunstmaanExtraBundle\DependencyInjection\CompilerPass\ElasticSearchCompilerPass;
use ArsThanea\KunstmaanExtraBundle\DependencyInjection\CompilerPass\ImagineChainedDataLoaderCompilerPass;
use Nassau\RegistryCompiler\RegistryCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanExtraBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ElasticSearchCompilerPass());
        $container->addCompilerPass(new ImagineChainedDataLoaderCompilerPass());

        foreach ($container->getCompilerPassConfig()->getPasses() as $pass) {
            if ($pass instanceof RegistryCompilerPass) {
                return;
            }
        };

        $container->addCompilerPass(new RegistryCompilerPass, PassConfig::TYPE_OPTIMIZE);
    }

}
