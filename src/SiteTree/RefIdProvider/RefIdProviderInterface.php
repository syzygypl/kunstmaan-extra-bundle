<?php


namespace ArsThanea\KunstmaanExtraBundle\SiteTree\RefIdProvider;


use ArsThanea\KunstmaanExtraBundle\SiteTree\HasRefInterface;

interface RefIdProviderInterface
{
    /**
     * @param mixed $value
     *
     * @return HasRefInterface|null
     */
    public function getRefId($value);


}