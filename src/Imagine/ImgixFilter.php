<?php

namespace ArsThanea\KunstmaanExtraBundle\Imagine;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class ImgixFilter implements ImageResizer
{
    /**
     * @var ImgixSerializer
     */
    private $serializer;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var array
     */
    private $presets = [];

    /**
     * @param ImgixSerializer $serializer
     * @param CacheManager $cacheManager
     * @param array $presets
     */
    public function __construct(ImgixSerializer $serializer, CacheManager $cacheManager, array $presets = [])
    {
        $this->serializer = $serializer;
        $this->cacheManager = $cacheManager;
        $this->presets = $presets;
    }

    public function imgixFilter($path, $name, array $transformations)
    {
        if (isset($this->presets[$name])) {
            $transformations += $this->presets[$name];
        }

        if (sizeof($transformations)) {
            $path = parse_url($path, PHP_URL_PATH);
            $path = $this->serializer->serialize($path, $transformations);
        }

        return $this->cacheManager->getBrowserPath($path, $name);
    }

    /**
     * @param string $path
     * @param string $filter
     * @param integer $width
     * @return string
     */
    public function getBrowserPath($path, $filter, $width)
    {
        if (isset($this->presets[$filter])) {
            return $this->imgixFilter($path, $filter, ['w' => $width]);
        }

        return $this->cacheManager->getBrowserPath($path, $filter, [
            'relative_resize' => [
                'widen' => $width,
            ],
        ]);
    }
}
