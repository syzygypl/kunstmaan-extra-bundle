<?php

namespace ArsThanea\KunstmaanExtraBundle\SiteTree\RefIdProvider;

use ArsThanea\KunstmaanExtraBundle\SiteTree\HasRefInterface;

class RefIdValueObject implements HasRefInterface
{
    private $refId;

    public function __construct($refId)
    {
        $this->refId = $refId;
    }

    /**
     * @return string
     */
    public function getRefName()
    {
        throw new \LogicException('This ref object has only and ID');
    }

    /**
     * @return int
     */
    public function getRefId()
    {
        return $this->refId;
    }
}
