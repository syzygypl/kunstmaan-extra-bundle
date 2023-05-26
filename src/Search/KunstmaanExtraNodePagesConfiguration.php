<?php

namespace ArsThanea\KunstmaanExtraBundle\Search;

use ArsThanea\KunstmaanExtraBundle\ContentType\PageContentTypeInterface;
use Elastica\Index;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeSearchBundle\Configuration\NodePagesConfiguration;
use Kunstmaan\SearchBundle\Search\AnalysisFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Kunstmaan\SearchBundle\Provider\SearchProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Custom search configuration for Kunstmaan Search Engine
 */
class KunstmaanExtraNodePagesConfiguration extends NodePagesConfiguration
{
    private EventDispatcherInterface $eventDispatcher;
    private PageContentTypeInterface $contentType;
    /**
     * @var int kunstmaan_extra.search.shards
     */
    private int $shards = 4;
    /**
     * @var int kunstmaan_extra.search.replicas
     */
    private int $replicas = 1;

    /**
     * @param string $name kunstmaan_node_search.indexname
     * @param string $type kunstmaan_node_search.indextype
     */
    public function __construct(
        ContainerInterface      $container,
        SearchProviderInterface $searchProvider,
        string                  $name,
        string                  $type,
        EventDispatcherInterface $eventDispatcher,
        PageContentTypeInterface $contentType
    )
    {
        parent::__construct($container, $searchProvider, $name, $type);
        $this->eventDispatcher = $eventDispatcher;
        $this->contentType = $contentType;
        $this->shards = $this->container->getParameter('kunstmaan_extra.search.shards');
        $this->replicas = $this->container->getParameter('kunstmaan_extra.search.replicas');
    }

    public function setAnalysis(Index $index, AnalysisFactoryInterface $analysis)
    {
        $index->create(
            array(
                'number_of_shards'   => $this->shards,
                'number_of_replicas' => $this->replicas,
                'analysis'           => $analysis->build()
            )
        );
    }


    protected function addSearchType($page, &$doc): void
    {
        parent::addSearchType($page, $doc);

        if (str_contains($doc['type'], "\\")) {
            $doc['type'] = $this->contentType->getFriendlyName($doc['type']);
        }
    }


    /**
     * @param NodeTranslation $nodeTranslation
     * @param bool|false      $add
     *
     * @return bool
     */
    public function indexNodeTranslation(NodeTranslation $nodeTranslation, $add = false): bool
    {
        $event = new NodeTranslationEvent($nodeTranslation);

        $this->eventDispatcher->dispatch(NodeTranslationEvent::NODE_TRANSLATION_INDEX_BEFORE, $event);

        if ($event->isIndexable()) {
            return parent::indexNodeTranslation($nodeTranslation, $add);
        } else {
            $this->deleteNodeTranslation($nodeTranslation);

            return false;
        }
    }
}
