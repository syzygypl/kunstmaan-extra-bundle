<?php

namespace ArsThanea\KunstmaanExtraBundle\Search;

class DocumentReference
{
    /**
     * @var array
     */
    private $document;

    /**
     * @var string
     */
    private $uid;

    /**
     * @var string
     */
    private $indexName;

    /**
     * @var string
     */
    private $indexType;

    public function __construct($document, $uid, $indexName, $indexType)
    {
        $this->document = $document;
        $this->uid = $uid;
        $this->indexName = $indexName;
        $this->indexType = $indexType;
    }

    /**
     * @return array
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @return string
     */
    public function getIndexName()
    {
        return $this->indexName;
    }

    /**
     * @return string
     */
    public function getIndexType()
    {
        return $this->indexType;
    }


}
