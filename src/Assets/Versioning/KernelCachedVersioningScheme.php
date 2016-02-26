<?php

namespace ArsThanea\KunstmaanExtraBundle\Assets\Versioning;

class KernelCachedVersioningScheme implements AssetVersioningSchemeInterface
{
    /**
     * @var
     */
    private $cache = [];

    /**
     * @param string $cachePath
     */
    public function __construct($cachePath)
    {
        if (file_exists($cachePath)) {
            $this->cache = include $cachePath;
        }
    }

    /**
     * @param string $assetPath
     *
     * @return string|null
     */
    public function getVersion($assetPath)
    {
        $assetPath = parse_url($assetPath, PHP_URL_PATH);

        if (isset($this->cache[$assetPath])) {
            return $this->cache[$assetPath];
        }

        return null;
    }
}
