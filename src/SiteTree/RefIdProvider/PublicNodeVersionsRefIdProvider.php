<?php

namespace ArsThanea\KunstmaanExtraBundle\SiteTree\RefIdProvider;

use ArsThanea\KunstmaanExtraBundle\ContentCategory\Category;
use ArsThanea\KunstmaanExtraBundle\SiteTree\HasRefInterface;
use ArsThanea\KunstmaanExtraBundle\SiteTree\PublicNodeVersions;
use Elastica\Result;

class PublicNodeVersionsRefIdProvider implements RefIdProviderInterface
{
    /**
     * @var PublicNodeVersions
     */
    private $nodeVersions;

    public function __construct(PublicNodeVersions $publicNodeVersions)
    {
        $this->nodeVersions = $publicNodeVersions;
    }

    /**
     * @param mixed $value
     *
     * @return HasRefInterface|null
     */
    public function getRefId($value)
    {
        $id = $this->getValueId($value);

        return $id ? new RefIdValueObject($id) : null;
    }

    private function getValueId($value)
    {
        if ($value instanceof Category) {
            return $this->nodeVersions->getNodeRef($value->getNodeId());
        } elseif ($value instanceof Result || (is_array($value) && isset($value['node_version_id']))) {
            $id = is_array($value) ? $value['node_version_id'] : $value->__get('node_version_id');
            return $this->nodeVersions->getRef($id);
        }

        return null;
    }
}
