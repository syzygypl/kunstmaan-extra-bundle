<?php


namespace ArsThanea\KunstmaanExtraBundle\Url;


interface AssetUrlServiceInterface
{

    /**
     * @param string $path
     *
     * @return string
     */
    public function getAssetUrl($path);
}