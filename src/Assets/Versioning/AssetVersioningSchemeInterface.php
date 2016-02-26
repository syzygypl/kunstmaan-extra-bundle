<?php

namespace ArsThanea\KunstmaanExtraBundle\Assets\Versioning;

interface AssetVersioningSchemeInterface
{
    /**
     * @param string $assetPath
     *
     * @return string|null
     */
    public function getVersion($assetPath);
}
