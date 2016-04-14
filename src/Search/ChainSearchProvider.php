<?php

namespace ArsThanea\KunstmaanExtraBundle\Search;

use Elastica\Index;
use Kunstmaan\SearchBundle\Provider\SearchProviderChainInterface;
use Kunstmaan\SearchBundle\Provider\SearchProviderInterface;

class ChainSearchProvider implements SearchProviderInterface
{
    /**
     * @var SearchProviderChainInterface
     */
    private $searchProviderChain;
    /**
     * @var string
     */
    private $indexNamePrefix;

    /**
     * @param SearchProviderChainInterface $searchProviderChain
     * @param string                       $indexNamePrefix
     * @param string                       $activeProvider
     */
    public function __construct(SearchProviderChainInterface $searchProviderChain, $indexNamePrefix, $activeProvider)
    {
        $this->searchProviderChain = $searchProviderChain;
        $this->indexNamePrefix = $indexNamePrefix;
    }

    /**
     * Returns a unique name for the SearchProvider
     *
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_extra_chain_provider';
    }

    /**
     * Return the client object
     *
     * @return mixed
     */
    public function getClient()
    {
        $client = $this->reduceFirst(function (SearchProviderInterface $provider) {
            return $provider->getClient();
        });

        if (null === $client) {
            throw new \RuntimeException('No provider was able to return a client');
        }

        return $client;
    }

    /**
     * Create an index
     *
     * @param string $indexName Name of the index
     * @return Index
     */
    public function createIndex($indexName)
    {
        $indexes = array_filter($this->mapProviders(function (SearchProviderInterface $provider) use ($indexName) {
            return $provider->createIndex($this->indexNamePrefix . $indexName);
        }));

        return reset($indexes);
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
        return $this->reduceFirst(function (SearchProviderInterface $provider) use ($indexName) {
            return $provider->getIndex($this->indexNamePrefix . $indexName);
        });
    }

    /**
     * Create a document
     *
     * @param string $uid
     * @param mixed $document
     * @param string $indexName
     * @param string $indexType
     *
     * @return mixed
     */
    public function createDocument($document, $uid, $indexName = '', $indexType = '')
    {
        return new DocumentReference($document, $uid, $this->indexNamePrefix . $indexName, $indexType);
    }

    /**
     * Add a document to the index
     *
     * @param string $indexName Name of the index
     * @param string $indexType Type of the index to add the document to
     * @param array $document The document to index
     * @param string $uid Unique ID for this document, this will allow the document to be overwritten by new data
     *                          instead of being duplicated
     */
    public function addDocument($indexName, $indexType, $document, $uid)
    {
        if ("" === $indexName && $document instanceof DocumentReference) {
            $indexName = $document->getIndexName();
        } else {
            $indexName = $this->indexNamePrefix . $indexName;
        }

        $this->mapProviders(function (SearchProviderInterface $provider) use ($document, $uid, $indexName, $indexType) {
            $provider->addDocument($document, $uid, $indexName, $indexType);
        });
    }

    /**
     * Add a collection of documents at once
     *
     * @param mixed $documents
     * @param string $indexName Name of the index
     * @param string $indexType Type of the index the document is located
     *
     * @return mixed
     */
    public function addDocuments($documents, $indexName = '', $indexType = '')
    {
        if ("" === $indexName && isset($documents[0])) {
            $indexName = $documents[0]->getIndexName();
        } else {
            $indexName = $this->indexNamePrefix . $indexName;
        }

        $this->mapProviders(function (SearchProviderInterface $provider) use ($documents, $indexName, $indexType) {

            $documents = array_map(function (DocumentReference $document) use ($provider) {
                return $provider->createDocument(
                    $document->getDocument(),
                    $document->getUid(),
                    $document->getIndexName(),
                    $document->getIndexType()
                );
            }, $documents);

            $provider->addDocuments($documents, $indexName, $indexType);
        });
    }

    /**
     * delete a document from the index
     *
     * @param string $indexName Name of the index
     * @param string $indexType Type of the index the document is located
     * @param string $uid Unique ID of the document to be delete
     */
    public function deleteDocument($indexName, $indexType, $uid)
    {
        $this->mapProviders(function (SearchProviderInterface $provider) use ($indexName, $indexType, $uid) {
            $provider->deleteDocument($this->indexNamePrefix . $indexName, $indexType, $uid);
        });
    }

    /**
     * @param string $indexName
     * @param string $indexType
     * @param array $ids
     */
    public function deleteDocuments($indexName, $indexType, array $ids)
    {
        $this->mapProviders(function (SearchProviderInterface $provider) use ($indexName, $indexType, $ids) {
            $provider->deleteDocuments($this->indexNamePrefix . $indexName, $indexType, $ids);
        });
    }

    /**
     * Delete an index
     *
     * @param string $indexName Name of the index to delete
     */
    public function deleteIndex($indexName)
    {
        $this->mapProviders(function (SearchProviderInterface $provider) use ($indexName) {
            try {
                $provider->deleteIndex($this->indexNamePrefix . $indexName);
            } catch (\Exception $e) {
                // we don’t care
            }
        });
    }

    private function mapProviders(\Closure $callback)
    {
        return array_map($callback, $this->searchProviderChain->getProviders());
    }

    private function reduceFirst(\Closure $callback)
    {
        return array_reduce($this->searchProviderChain->getProviders(), function($found, $provider) use ($callback) {
            return $found ?: $callback($provider);
        }, null);
    }
}
