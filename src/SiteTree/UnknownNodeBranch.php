<?php

namespace ArsThanea\KunstmaanExtraBundle\SiteTree;

/**
 * Class UnknownNodeBranch
 *
 * @internal
 */
class UnknownNodeBranch extends Branch
{

    public function __construct(Branch $branch = null)
    {
        parent::__construct(null, null, null, null, null, null);
        if ($branch) {
            $this->add($branch);
        }
    }

    public function getTitle()
    {
        throw new \LogicException('This is an unknow node, you can’t query it for title');
    }

    public function getSlug()
    {
        throw new \LogicException('This is an unknow node, you can’t query it for slug');
    }

    public function getRefId()
    {
        throw new \LogicException('This is an unknow node, you can’t query it for refId');
    }

    public function getNodeId()
    {
        throw new \LogicException('This is an unknow node, you can’t query it for nodeId');
    }

    public function getRefName()
    {
        throw new \LogicException('This is an unknow node, you can’t query it for refName');
    }

    public function getInternalName()
    {
        throw new \LogicException('This is an unknow node, you can’t query it for internalName');
    }


}
