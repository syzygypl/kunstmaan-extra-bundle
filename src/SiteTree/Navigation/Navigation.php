<?php

namespace ArsThanea\KunstmaanExtraBundle\SiteTree\Navigation;

use ArsThanea\KunstmaanExtraBundle\ContentCategory\ContentCategoryInterface;
use ArsThanea\KunstmaanExtraBundle\SiteTree\BranchUtilitiesService;
use ArsThanea\KunstmaanExtraBundle\SiteTree\SiteTreeService;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

class Navigation
{

    /**
     * @var SiteTreeService
     */
    private $siteTree;

    /**
     * @var ContentCategoryInterface
     */
    private $contentCategory;

    /**
     * @var BranchUtilitiesService
     */
    private $utility;

    /**
     * @var array
     */
    private $nav = [];

    /**
     * @param SiteTreeService $siteTree
     * @param ContentCategoryInterface $contentCategory
     * @param BranchUtilitiesService $utility
     */
    public function __construct(SiteTreeService $siteTree, ContentCategoryInterface $contentCategory, BranchUtilitiesService $utility)
    {
        $this->siteTree = $siteTree;
        $this->contentCategory = $contentCategory;
        $this->utility = $utility;
    }


    public function getNextPage(HasNodeInterface $page)
    {
        list (, $next) = $this->getPrevNext($page);

        return $next;
    }

    public function getPreviousPage(HasNodeInterface $page)
    {
        list ($prev) = $this->getPrevNext($page);

        return $prev;
    }

    private function getPrevNext(HasNodeInterface $page)
    {
        $class = ClassLookup::getClass($page);
        $id = $page->getId();

        if (false === isset($this->nav[$class][$id])) {
            $parent = $this->contentCategory->getParentCategory($page);
            $siblings = $this->siteTree->getChildren($parent, [
                'refName' => $class,
                'depth' => 1,
            ]);

            $current = null;

            foreach ($siblings->getChildren() as $item) {
                if ((string)$item->getRefId() === (string)$page->getId() && $item->getRefName() === $class) {
                    $current = $item;
                    break;
                }
            }

            if (null === $current) {
                $this->nav[$class][$id] = [null, null];
            } else {
                $this->nav[$class][$id] = [
                    $this->utility->getPreviousSibling($siblings, $current),
                    $this->utility->getNextSibling($siblings, $current),
                ];
            }

        }

        return $this->nav[$class][$id];
    }


}
