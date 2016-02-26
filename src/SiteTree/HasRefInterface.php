<?php

namespace ArsThanea\KunstmaanExtraBundle\SiteTree;

interface HasRefInterface 
{
    /**
     * @return string
     */
    public function getRefName();

    /**
     * @return int
     */
    public function getRefId();
}