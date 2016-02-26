<?php

namespace ArsThanea\KunstmaanExtraBundle\Url;

use ArsThanea\KunstmaanExtraBundle\Assets\Versioning\AssetVersioningSchemeInterface;

class AssetUrlService implements AssetUrlServiceInterface
{
    /**
     * @var string
     */
    private $assetCdn;

    /**
     * @var AssetVersioningSchemeInterface
     */
    private $versioningScheme;

    /**
     * @param string $assetCdn
     */
    public function __construct($assetCdn)
    {
        $this->assetCdn = $assetCdn;
    }

    /**
     * @param AssetVersioningSchemeInterface $versioningScheme
     *
     * @return $this
     */
    public function setVersioningScheme(AssetVersioningSchemeInterface $versioningScheme)
    {
        $this->versioningScheme = $versioningScheme;

        return $this;
    }


    public function getAssetUrl($path)
    {
        return $this->assetCdn . $this->version($path);
    }

    private function version($path)
    {
        if (null === $this->versioningScheme) {
            return $path;
        }

        $version = $this->versioningScheme->getVersion($path);

        if (!$version) {
            return $path;
        }

        return sprintf('%s?%s', $path, $version);
    }

}
