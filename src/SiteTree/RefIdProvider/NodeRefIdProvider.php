<?php


namespace ArsThanea\KunstmaanExtraBundle\SiteTree\RefIdProvider;

use ArsThanea\KunstmaanExtraBundle\SiteTree\CurrentLocaleInterface;
use ArsThanea\KunstmaanExtraBundle\SiteTree\HasRefInterface;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;

class NodeRefIdProvider implements RefIdProviderInterface
{

    /**
     * @var CurrentLocaleInterface
     */
    private $currentLocale;

    /**
     * @param CurrentLocaleInterface $currentLocale
     */
    public function __construct(CurrentLocaleInterface $currentLocale)
    {
        $this->currentLocale = $currentLocale;
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
        if ($value instanceof HasNodeInterface) {
            return $value->getId();
        } elseif ($value instanceof HasRefInterface) {
            return $value->getRefId();
        } elseif ($value instanceof NodeVersion) {
            return $value->getRefId();
        } elseif ($value instanceof NodeTranslation) {
            $nodeVersion = $value->getPublicNodeVersion();

            return $nodeVersion ? $nodeVersion->getRefId() : null;
        } elseif ($value instanceof Node) {
            $nodeTranslation = $value->getNodeTranslation($this->currentLocale->getCurrentLocale(), true);

            return $nodeTranslation ? $nodeTranslation->getPublicNodeVersion()->getRefId() : null;
        }

        return null;
    }
}
