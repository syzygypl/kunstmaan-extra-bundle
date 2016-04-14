<?php

namespace ArsThanea\KunstmaanExtraBundle\Search;

use Kunstmaan\SearchBundle\Provider\SearchProviderInterface;

class NullSearchProvider implements SearchProviderInterface
{
    public function createIndex($indexName)
    {
        // noop
    }

    public function addDocument($indexName, $indexType, $document, $uid)
    {
        // noop
    }

    public function addDocuments($documents, $indexName = '', $indexType = '')
    {
        // noop
    }

    public function deleteDocument($indexName, $indexType, $uid)
    {
        // noop
    }

    public function deleteDocuments($indexName, $indexType, array $ids)
    {
        // noop
    }

    public function deleteIndex($indexName)
    {
        // noop
    }

    /**
     * Returns a unique name for the SearchProvider
     *
     * @return string
     */
    public function getName()
    {
        return 'null';
    }

    /**
     * Return the client object
     *
     * @return mixed
     */
    public function getClient()
    {
        return null;
    }

    /**
     * Return the index object
     *
     * @param $indexName
     *
     * @return mixed
     */
    public function getIndex($indexName)
    {
        return null;
    }

    /**
     * Create a document
     *
     * @param string $uid
     * @param mixed  $document
     * @param string $indexName
     * @param string $indexType
     *
     * @return mixed
     */
    public function createDocument($document, $uid, $indexName = '', $indexType = '')
    {
        return $document;
    }
}
