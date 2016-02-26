<?php

namespace ArsThanea\KunstmaanExtraBundle\SiteTree;

class BranchUtilitiesService
{

    /**
     * @param Branch $tree
     * @param int    $nodeId
     *
     * @return Branch|null
     */
    public function findBranchByNodeId(Branch $tree, $nodeId)
    {
        foreach ($tree->getChildren() as $branch) {
            if ($branch->getNodeId() == $nodeId) {
                return $branch;
            }

            // recursive search:
            $branch = $this->findBranchByNodeId($branch, $nodeId);
            if ($branch) {
                return $branch;
            }
        }

        return null;
    }

    /**
     * @param Branch $branch
     * @param Branch $currentBranch
     *
     * @return array
     * @throws \Exception
     */
    public function getNextSibling(Branch $branch, Branch $currentBranch)
    {
        $current = null;
        $nextSibiling = null;

        foreach ($branch->getChildren() as $node) {
            if ($current) {
                $nextSibiling = $node;
                break;
            }
            if ($node->getNodeId() === $currentBranch->getNodeId()) {
                $current = $node;
            }
        }

        return $nextSibiling;
    }

    /**
     * @param Branch $branch
     * @param Branch $currentBranch
     *
     * @return array
     * @throws \Exception
     */
    public function getPreviousSibling(Branch $branch, Branch $currentBranch)
    {
        $current = null;
        $previousSibiling = null;

        foreach ($branch->getChildren() as $node) {
            if ($node->getNodeId() === $currentBranch->getNodeId()) {
                $current = $node;
            }
            if (!$current) {
                $previousSibiling = $node;
            }
            if ($previousSibiling && $current) {
                break;
            }
        }

        return $previousSibiling;
    }

    /**
     * @param Branch $branch
     *
     * @return Branch|null
     */
    public function getFirstChild(Branch $branch = null)
    {
        if (null === $branch) {
            return null;
        }

        //reset return first child or false if is not found
        $children = $branch->getChildren();
        $first = reset($children);

        return $first ? $first : null;
    }

    /**
     * @param Branch $branch
     *
     * @return Branch|null
     */
    public function getLastChild(Branch $branch = null)
    {
        if (null === $branch) {
            return null;
        }

        //end return last child or false if is not found
        $children = $branch->getChildren();
        $last = end($children);

        return $last ? $last : null;
    }
}
