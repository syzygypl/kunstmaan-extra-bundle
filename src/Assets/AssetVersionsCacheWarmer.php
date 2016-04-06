<?php

namespace ArsThanea\KunstmaanExtraBundle\Assets;

use ArsThanea\KunstmaanExtraBundle\Assets\Versioning\AssetVersioningSchemeInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;

class AssetVersionsCacheWarmer extends CacheWarmer
{

    /**
     * @var AssetVersioningSchemeInterface
     */
    private $versioningScheme;

    /**
     * @var string
     */
    private $assetsDir;

    /**
     * @var string
     */
    private $cacheName;

    /**
     * @param AssetVersioningSchemeInterface $versioningScheme
     * @param string                         $assetsDir
     * @param string                         $cachePath
     */
    public function __construct(AssetVersioningSchemeInterface $versioningScheme, $assetsDir, $cachePath)
    {
        $this->versioningScheme = $versioningScheme;
        $this->assetsDir = $assetsDir;
        $this->cacheName = basename($cachePath);
    }


    /**
     * Warms up the cache.
     *
     * @param string $cacheDir The cache directory
     */
    public function warmUp($cacheDir)
    {
        if (false === is_dir($this->assetsDir)) {
            return;
        }

        $finder = new Finder();
        $finder->files()->in($this->assetsDir);

        $result = [];

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $cannonicalName = $file->getRelativePathname();

            $result[$cannonicalName] = $this->versioningScheme->getVersion('/' . $cannonicalName);
        }

        $cacheDir = sprintf($cacheDir . '/' . $this->cacheName);
        $this->writeCacheFile($cacheDir, sprintf('<?php return %s;', var_export($result, true)));

    }

    /**
     * Checks whether this warmer is optional or not.
     *
     * Optional warmers can be ignored on certain conditions.
     *
     * A warmer should return true if the cache can be
     * generated incrementally and on-demand.
     *
     * @return bool true if the warmer is optional, false otherwise
     */
    public function isOptional()
    {
        return false;
    }
}
