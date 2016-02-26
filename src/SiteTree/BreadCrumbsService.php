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

    /**
     * @var string
     */
    private $lang;

    public function __construct(NodeRepository $nodeRepository, $lang)
    {
        $this->nodeRepository = $nodeRepository;
        $this->lang = $lang;
    }

    /**
     * [Home -> Category -> SubCategory -> Page]
     *
     * @param Node $node
     *
     * @see getBreadCrumbs
     * @see getParents
     *
     * @return Node[]
     */
    public function getNodePath(Node $node)
    {
        return $this->nodeRepository->getAllParents($node, $this->lang);
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