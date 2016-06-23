<?php

namespace ArsThanea\KunstmaanExtraBundle\SiteTree\Navigation;

use ArsThanea\KunstmaanExtraBundle\ContentCategory\ContentCategoryInterface;
use ArsThanea\KunstmaanExtraBundle\SiteTree\Branch;
use ArsThanea\KunstmaanExtraBundle\SiteTree\BranchUtilitiesService;
use ArsThanea\KunstmaanExtraBundle\SiteTree\SiteTreeService;
use ArsThanea\KunstmaanExtraBundle\SiteTree\UnknownNodeBranch;
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


    public function getNextPage(HasNodeInterface $page, $loop = false)
    {
        list (, $next) = $this->getPrevNext($page, $loop);

        return $next;
    }

    public function getPreviousPage(HasNodeInterface $page, $loop = false)
    {
        list ($prev) = $this->getPrevNext($page, $loop);

        return $prev;
    }

    private function getPrevNext(HasNodeInterface $page, $loop)
    {
        $class = ClassLookup::getClass($page);
        $id = $page->getId();

        if (false === isset($this->nav[$class][$id])) {
            $parent = $this->contentCategory->getParentCategory($page, true);
            $siblings = $this->siteTree->getChildren($parent, [
                'refName' => $class,
                'depth' => 1,
            ]) ?: new UnknownNodeBranch();

            $this->nav[$class][$id] = $this->calculatePrevNext($siblings, $page, $loop);
        }

        return array_values($this->nav[$class][$id]);
    }

    private function calculatePrevNext(Branch $siblings, HasNodeInterface $page, $loop)
    {
        $result = ['prev' => null, 'next' => null];

        // zero or one results (current page) means there is nowhere to navigate to
        if (1 >= sizeof($siblings->getChildren())) {
            return $result;
        }

        $current = null;

        foreach ($siblings->getChildren() as $item) {
            if ((string)$item->getRefId() === (string)$page->getId()) {
                $current = $item;
                break;
            }
        }

        if (null !== $current) {
            $result = [
                'prev' => $this->utility->getPreviousSibling($siblings, $current),
                'next' => $this->utility->getNextSibling($siblings, $current),
            ];
        }

        if ($loop) {
            $result['prev'] = $result['prev'] ?: $this->utility->getLastChild($siblings);
            $result['next'] = $result['next'] ?: $this->utility->getFirstChild($siblings);
        }

        return $result;
    }


}
