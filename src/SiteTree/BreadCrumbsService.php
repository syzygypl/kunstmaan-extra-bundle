<?php


namespace ArsThanea\KunstmaanExtraBundle\SiteTree;


use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Repository\NodeRepository;

class BreadCrumbsService
{

    /**
     * @var NodeRepository
     */
    private $nodeRepository;

    public function __construct(NodeRepository $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }

    /**
     * [Home -> Category -> SubCategory -> Page]
     *
     * @param Node $node
     *
     * @return \Kunstmaan\NodeBundle\Entity\Node[]
     * @see getBreadCrumbs
     * @see getParents
     *
     */
    public function getNodePath(Node $node)
    {
        return $this->nodeRepository->getAllParents($node);
    }

    /**
     * [Home -> Category -> SubCategory] -> Page
     *
     * @param Node $node
     *
     * @see getBreadCrumbs
     * @see getNodePath
     *
     * @return Node[]
     */
    public function getParents(Node $node)
    {
        return array_slice($this->getNodePath($node), 0, -1);
    }

    /**
     * Home -> [Category -> SubCategory -> Page]
     *
     * @param Node $node
     *
     * @see getParents
     * @see getNodePath
     *
     * @return Node[]
     */
    public function getBreadcrumbs(Node $node)
    {
        return array_slice($this->getNodePath($node), 1);
    }

}
