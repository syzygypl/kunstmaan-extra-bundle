<?php

namespace ArsThanea\KunstmaanExtraBundle\Assets\Versioning;

class Md5VersioningScheme implements AssetVersioningSchemeInterface
{
    /**
     * @var
     */
    private $assetsDir;

    public function __construct($assetsDir)
    {
        $this->assetsDir = $assetsDir;
    }

    public function getVersion($assetPath)
    {
        return substr(md5_file($this->assetsDir . parse_url($assetPath, PHP_URL_PATH)), 0, 7);
    }
}
