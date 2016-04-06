<?php

namespace ArsThanea\KunstmaanExtraBundle\Assets\Versioning;

class KernelCachedVersioningScheme implements AssetVersioningSchemeInterface
{
    /**
     * @var
     */
    private $cache = [];

    /**
     * @var string
     */
    private $webPrefix;

    /**
     * @param string $cachePath
     */
    public function __construct($cachePath, $webPrefix)
    {
        if (file_exists($cachePath)) {
            $this->cache = include $cachePath;
        }

        $this->webPrefix = '/' . trim($webPrefix, '/') . '/';
    }

    /**
     * @param string $assetPath
     *
     * @return string|null
     */
    public function getVersion($assetPath)
    {
        $assetPath = parse_url($assetPath, PHP_URL_PATH);

        if ($this->webPrefix !== substr($assetPath, 0, strlen($this->webPrefix))) {
            return null;
        }

        $assetPath = substr($assetPath, strlen($this->webPrefix));

        if (isset($this->cache[$assetPath])) {
            return $this->cache[$assetPath];
        }

        return null;
    }
}
