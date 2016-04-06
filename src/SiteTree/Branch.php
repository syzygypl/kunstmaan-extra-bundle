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
     * @var string
     */
    private $lang;

    /**
     * @var Branch[]
     */
    private $children = [];

    /**
     * @param string $title
     * @param int $nodeId
     * @param string $slug
     * @param string $lang
     * @param int $refId
     * @param string $refName
     * @param string $internalName
     */
    public function __construct($title, $nodeId, $slug, $lang, $refId, $refName, $internalName)
    {
        parent::__construct($title, $nodeId, $slug);
        $this->lang = $lang;
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

    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @return \Iterator|Branch[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->children);
    }

}
