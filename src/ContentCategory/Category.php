<?php

namespace ArsThanea\KunstmaanExtraBundle\ContentCategory;

class Category implements \JsonSerializable
{
    /**
     * @var int
     */
    private $nodeId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $slug;

    public function __construct($title, $nodeId, $slug)
    {
        $this->nodeId = $nodeId;
        $this->title = $title;
        $this->slug = $slug;
    }

    /**
     * @return int
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @TODO: rename to url
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    public function __toString()
    {
        return $this->getTitle();
    }


    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *       which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return [
            'node_id' => $this->nodeId,
            'slug' => $this->slug,
            'title' => $this->title,
        ];
    }
}