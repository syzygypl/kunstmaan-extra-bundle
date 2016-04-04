<?php

namespace ArsThanea\KunstmaanExtraBundle\ContentCategory;

use ArsThanea\KunstmaanExtraBundle\SiteTree\BreadCrumbsService;
use ArsThanea\KunstmaanExtraBundle\SiteTree\PublicNodeVersions;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

class ContentCategoryService implements ContentCategoryInterface
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var PublicNodeVersions
     */
    private $nodeVersions;
    /**
     * @var BreadCrumbsService
     */
    private $breadCrumbsService;

    public function __construct(Cache $cache, PublicNodeVersions $nodeVersions, BreadCrumbsService $breadCrumbsService)
    {
        $this->cache = $cache;
        $this->nodeVersions = $nodeVersions;
        $this->breadCrumbsService = $breadCrumbsService;
    }

    /**
     * @param HasNodeInterface $page
     *
     * @return Category
     */
    public function getRootCategory(HasNodeInterface $page)
    {
        $breadcrumbs = $this->getBreadcrumbs($page);

        return reset($breadcrumbs);
    }

    /**
     * @param HasNodeInterface $page
     *
     * @return Category
     */
    public function getMainCategory(HasNodeInterface $page)
    {
        $breadcrumbs = $this->getBreadcrumbs($page);

        if (sizeof($breadcrumbs) > 1) {
            array_shift($breadcrumbs);
        }

        return reset($breadcrumbs);
    }

    /**
     * @param HasNodeInterface $page
     *
     * @param bool             $hidden
     *
     * @return Category|null
     */
    public function getParentCategory(HasNodeInterface $page, $hidden = false)
    {
        $breadcrumbs = $this->getBreadcrumbs($page, $hidden);

        // chop of current page, as it cant be it’s own parent:
        $breadcrumbs = array_slice($breadcrumbs, 0, -1);

        return end($breadcrumbs) ?: null;
    }

    /**
     * @param HasNodeInterface $page
     * @param bool             $full
     *
     * @invalidate cache on structure change
     * @return Category[]
     */
    public function getBreadcrumbs(HasNodeInterface $page, $full = false)
    {
        $key = sprintf('%s:%d:%d', ClassLookup::getClass($page), $page->getId(), $full);
        if ($this->cache->contains($key)) {
            return $this->cache->fetch($key);
        }

        $node = $this->nodeVersions->getNodeFor($page);

        if (null === $node) {
            throw new \RuntimeException(sprintf('Cant find node for %s:%d page', get_class($page), $page->getId()));
        }

        $breadcrumbs = $this->breadCrumbsService->getNodePath($node);

        $parents = (new ArrayCollection($breadcrumbs))
            ->filter(function (Node $node) use ($full) { return $full || false === $node->isHiddenFromNav(); })
            ->map(function (Node $node) { return $node->getNodeTranslations(true)->first(); })
            ->map(function (NodeTranslation $nt) { return $this->nodeTranslationToCategory($nt); })
            ->toArray();

        $this->cache->save($key, $parents);

        return $parents;
    }

    private function nodeTranslationToCategory(NodeTranslation $nt)
    {
        return new Category($nt->getTitle(), $nt->getNode()->getId(), $nt->getUrl());
    }
}
