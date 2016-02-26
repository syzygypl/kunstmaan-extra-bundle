<?php

namespace ArsThanea\KunstmaanExtraBundle\SiteTree;

use ArsThanea\KunstmaanExtraBundle\ContentCategory\Category;

class Branch extends Category implements \IteratorAggregate, HasRefInterface
{
    /**
     * @var int
     */
    private $refId;

    /**
     * @var string
     */
    private $refName;

    /**
     * @var string
     */
    private $internalName;

    /**
     * @var Branch[]
     */
    private $children = [];

    /**
     * @param string $title
     * @param int $nodeId
     * @param string $slug
     * @param int $refId
     * @param string $refName
     * @param string $internalName
     */
    public function __construct($title, $nodeId, $slug, $refId, $refName, $internalName)
    {
        parent::__construct($title, $nodeId, $slug);
        $this->refId = $refId;
        $this->refName = $refName;
        $this->internalName = $internalName;
    }

    public function add(Branch $branch)
    {
        $this->children[] = $branch;

        return $this;
    }

    /**
     * @return string
     */
    public function getRefName()
    {
        return $this->refName;
    }

    /**
     * @return int
     */
    public function getRefId()
    {
        return $this->refId;
    }

    /**
     * @return Branch[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return string
     */
    public function getInternalName()
    {
        return $this->internalName;
    }

    /**
     * @return \Iterator|Branch[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->children);
    }

}
