<?php

namespace ArsThanea\KunstmaanExtraBundle\Search;

use Elastica\Index;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\NodeSearchBundle\Configuration\NodePagesConfiguration;
use Kunstmaan\NodeSearchBundle\Helper\SearchViewTemplateInterface;
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
     * @param ContainerInterface      $container
     * @param SearchProviderInterface $searchProvider
     * @param string                  $name
     * @param string                  $type
     */
    public function __construct($container, $searchProvider, $name, $type)
    {
        parent::__construct($container, $searchProvider, $name, $type);
        $this->eventDispatcher = $this->container->get('event_dispatcher');
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
