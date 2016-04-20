<?php


namespace ArsThanea\KunstmaanExtraBundle\DependencyInjection;


use ArsThanea\KunstmaanExtraBundle\Page\PageController\TypehintingControllerCacheWarmer;
use ArsThanea\KunstmaanExtraBundle\Search\ChainSearchProvider;
use ArsThanea\KunstmaanExtraBundle\Search\KunstmaanExtraNodePagesConfiguration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class KunstmaanExtraExtension extends Extension implements PrependExtensionInterface
{

    /**
     * Loads a specific configuration.
     *
     * @param array            $configs   An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configs = (new Processor)->processConfiguration(new Configuration(), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('twig_extensions.yml');
        $loader->load('form_types.yml');
        $loader->load('ref_id_providers.yml');


        $container->setParameter('kunstmaan_node_search.search_configuration.node.class', KunstmaanExtraNodePagesConfiguration::class);
        $container->setParameter('kunstmaan_search.search.class', ChainSearchProvider::class);

        if ($configs['search']['url']) {
            $container->setParameter(CompilerPass\ElasticSearchCompilerPass::PARAM_SEARCH_URL, $configs['search']['url']);
        }

        $container->setParameter('kunstmaan_extra.search.replicas', $configs['search']['replicas']);
        $container->setParameter('kunstmaan_extra.search.shards', $configs['search']['shards']);

        $container->setParameter('kunstmaan_extra.date_formats', isset($configs['date_formats']) ? $configs['date_formats'] : []);

        if ($configs['assets']['web_prefix']) {
            $container->setParameter('kunstmaan_extra.assets.cdn_url', $configs['assets']['cdn_url']);
            $container->setParameter('kunstmaan_extra.assets.web_prefix', $configs['assets']['web_prefix']);
            $loader->load('assets.yml');
        }

        if ($configs['generate_controller']) {
            $container->setDefinition('kunstmaan_extra.typehinting_controller_cache_warmer', (new Definition)
                ->setClass(TypehintingControllerCacheWarmer::class)
                ->setArguments([new Reference('kernel'), $configs['generate_controller']])
                ->addTag('kernel.cache_warmer')
            );
        }
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('doctrine_cache', [
            'providers' => [
                'kunstmaan_extra_content_category' => [
                    'type' => 'array'
                ]
            ]
        ]);
    }
}
