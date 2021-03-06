<?php

namespace ArsThanea\KunstmaanExtraBundle\Search;

use ArsThanea\KunstmaanExtraBundle\ContentType\PageContentTypeInterface;
use Elastica\Index;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\NodeSearchBundle\Configuration\NodePagesConfiguration;
use Kunstmaan\NodeSearchBundle\Helper\SearchViewTemplateInterface;
use Kunstmaan\SearchBundle\Search\AnalysisFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Kunstmaan\SearchBundle\Provider\SearchProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Templating\EngineInterface;

class KunstmaanExtraNodePagesConfiguration extends NodePagesConfiguration
{

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var PageContentTypeInterface
     */
    private $contentType;

    private $shards = 4;
    private $replicas = 1;

    /**
     * @param ContainerInterface      $container
     * @param SearchProviderInterface $searchProvider
     * @param string                  $name
     * @param string                  $type
     */
    public function __construct($container, $searchProvider, $name, $type)
    {
        parent::__construct($container, $searchProvider, $name, $type);
        $this->eventDispatcher = $this->container->get('event_dispatcher');
        $this->contentType = $this->container->get('kunstmaan_extra.content_type');
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


    protected function addSearchType($page, &$doc)
    {
        parent::addSearchType($page, $doc);

        if (false !== strpos($doc['type'], "\\")) {
            $doc['type'] = $this->contentType->getFriendlyName($doc['type']);
        }
    }


    /**
     * @param NodeTranslation $nodeTranslation
     * @param bool|false      $add
     *
     * @return bool
     */
    public function indexNodeTranslation(NodeTranslation $nodeTranslation, $add = false)
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

    /**
     * @inheritDoc
     * @todo: remove after PR #1108 is merged
     *
     * @see https://github.com/Kunstmaan/KunstmaanBundlesCMS/pull/1108
     */
    protected function renderCustomSearchView(NodeTranslation $nodeTranslation, SearchViewTemplateInterface $page, EngineInterface $renderer)
    {
        $view = $page->getSearchView();
        $renderContext = new RenderContext([
            'locale'          => $nodeTranslation->getLang(),
            'page'            => $page,
            'indexMode'       => true,
            'nodetranslation' => $nodeTranslation,
        ]);

        if ($page instanceof PageInterface) {
            $request = $this->container->get('request_stack')->getCurrentRequest();
            $page->service($this->container, $request, $renderContext);
        }

        $content = $this->removeHtml($renderer->render($view, $renderContext->getArrayCopy()));

        return $content;
    }
}
