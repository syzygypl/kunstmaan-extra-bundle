<?php

namespace ArsThanea\KunstmaanExtraBundle\SiteTree\Navigation;

use ArsThanea\KunstmaanExtraBundle\ContentCategory\ContentCategoryInterface;
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
            $this->nav[$class][$id] = ['prev' => null, 'next' => null];

            $parent = $this->contentCategory->getParentCategory($page, true);
            $siblings = $this->siteTree->getChildren($parent, [
                'refName' => $class,
                'depth' => 1,
            ]) ?: new UnknownNodeBranch();

            $current = null;

            foreach ($siblings->getChildren() as $item) {
                if ((string)$item->getRefId() === (string)$page->getId() && $item->getRefName() === $class) {
                    $current = $item;
                    break;
                }
            }

            if (null !== $current) {
                $this->nav[$class][$id] = [
                    'prev' => $this->utility->getPreviousSibling($siblings, $current),
                    'next' => $this->utility->getNextSibling($siblings, $current),
                ];
            }

            if ($loop) {
                $this->nav[$class][$id]['prev'] = $this->nav[$class][$id]['prev'] ?: $this->utility->getLastChild($siblings);
                $this->nav[$class][$id]['next'] = $this->nav[$class][$id]['next'] ?: $this->utility->getFirstChild($siblings);
            }
        }

        return array_values($this->nav[$class][$id]);
    }


}
