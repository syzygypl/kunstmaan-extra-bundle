<?php


namespace ArsThanea\KunstmaanExtraBundle\SiteTree\RefIdProvider;

use ArsThanea\KunstmaanExtraBundle\SiteTree\HasRefInterface;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;

class NodeRefIdProvider implements RefIdProviderInterface
{

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
        if ($value instanceof HasNodeInterface) {
            return $value->getId();
        } elseif ($value instanceof HasRefInterface) {
            return $value->getRefId();
        } elseif ($value instanceof Node) {
            return $value->getNodeTranslations(true)->first()->getPublicNodeVersion()->getRefId();
        } elseif ($value instanceof NodeTranslation) {
            return $value->getPublicNodeVersion()->getRefId();
        } elseif ($value instanceof NodeVersion) {
            return $value->getRefId();
        }

        return null;
    }
}