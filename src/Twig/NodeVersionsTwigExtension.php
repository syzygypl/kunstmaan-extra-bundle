<?php

namespace ArsThanea\KunstmaanExtraBundle\Twig;

use ArsThanea\KunstmaanExtraBundle\ContentCategory\Category;
use ArsThanea\KunstmaanExtraBundle\ContentType\PageContentTypeInterface;
use ArsThanea\KunstmaanExtraBundle\SiteTree\PublicNodeVersions;

class NodeVersionsTwigExtension extends \Twig_Extension
{

    /**
     * @var PublicNodeVersions
     */
    private $nodeVersions;

    /**
     * @var PageContentTypeInterface
     */
    private $contentTypeService;

    public function __construct(PublicNodeVersions $nodeVersions, PageContentTypeInterface $contentTypeService)
    {
        $this->nodeVersions = $nodeVersions;
        $this->contentTypeService = $contentTypeService;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('to_node', [$this->nodeVersions, 'getNodeFor']),
            new \Twig_SimpleFilter('to_node_version', [$this->nodeVersions, 'getNodeVersionFor']),
            new \Twig_SimpleFilter('to_node_translation', [$this->nodeVersions, 'getNodeTranslationFor']),
            new \Twig_SimpleFilter('to_*_page', [$this, 'categoryToPage']),
        ];
    }

    public function getFunctions()
    {
        return [
            'internal_node' => new \Twig_SimpleFunction('internal_node', [$this->nodeVersions, 'getBranchByInternalName']),
            'get_*_pages' => new \Twig_SimpleFunction('get_*_pages', [$this, 'getPagesOfType']),
        ];
    }


    public function categoryToPage($type, Category $category = null)
    {
        if (null === $category) {
            return null;
        }

        $class = $this->contentTypeService->getContentTypeClass($type);
        if (null === $class) {
            throw new \InvalidArgumentException("Can’t find page class name for $type");
        }

        return $this->nodeVersions->getCategoryRef($class, $category->getNodeId());

    }

    public function getPagesOfType($type)
    {
        $class = $this->contentTypeService->getContentTypeClass($type);
        if (null === $class) {
            throw new \InvalidArgumentException("Can’t find page class name for $type");
        }

        return $this->nodeVersions->getNodesOfType($class);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'node_versions_twig';
    }
}
