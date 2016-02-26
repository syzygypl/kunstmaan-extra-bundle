<?php

namespace ArsThanea\KunstmaanExtraBundle\SiteTree;

/**
 * Class TreeBuilder
 *
 * @internal
 */
class TreeBuilder
{
    /**
     * @var Branch[]
     */
    private $branches = [];

    /**
     * @var Branch
     */
    private $root;

    public function reset()
    {
        $this->branches = [];
        $this->root = null;

        return $this;
    }

    public function add($parentId, Branch $branch)
    {
        $this->branches[$branch->getNodeId()] = $branch;

        if (null === $this->root) {
            $this->root = $parentId ? ($this->branches[$parentId] = new UnknownNodeBranch($branch)) : $branch;
        } else {

            if (false === isset($this->branches[$parentId])) {
                // hidden from nav, don’t return the whole section then:
                return $this;
            }

            $this->branches[$parentId]->add($branch);
        }

        return $this;
    }

    /**
     * @return Branch
     */
    public function getRoot()
    {
        return $this->root;
    }
}