<?php

namespace ArsThanea\KunstmaanExtraBundle\Imagine;

use Kunstmaan\MediaBundle\Entity\Media;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class SrcsetCalculator implements ImageResizer
{
    /**
     * @var CacheManager
     */
    private $imagine;

    /**
     * @var array
     */
    private $breakpoints;

    /**
     * @var array
     */
    private $options;

    /**
     * @var ImageResizer
     */
    private $resizer;


    public function __construct(CacheManager $imagine, array $breakpoints, array $options = [])
    {
        $this->imagine = $imagine;
        $this->breakpoints = $breakpoints;
        $this->options = array_replace([
            'image_width_threshold' => 100,
            'default_filter' => 'srcset',
        ], $options);

        $this->setResizer($this);
        sort($this->breakpoints);
    }

    /**
     * @param Media|string $media
     * @param string $filter
     *
     * @return array
     */
    public function getSrcset($media, $filter = null)
    {
        $imageWidth = max(...$this->breakpoints);
        $imageUrl = $media;

        if ($media instanceof Media) {
            $imageWidth = array_replace(['original_width' => $imageWidth], $media->getMetadata())['original_width'];
            $imageUrl = $media->getUrl();
        }

        if ("" === (string)$imageUrl) {
            return [];
        }

        $filter = $filter ?: $this->options['default_filter'];

        $callback = function (array $result, $value) use ($imageUrl, $imageWidth, $filter) {
            if ($value >= $imageWidth - $this->options['image_width_threshold']) {
                $value = $imageWidth;
            }

            if (false === isset($result[$value])) {
                $result[$value] = $this->resizer->getBrowserPath($imageUrl, $filter, $value);
            }

            return $result;
        };

        $breakpoints = array_reduce($this->breakpoints, $callback, []);

        return $breakpoints;
    }

    /**
     * @param string $path
     * @param string $filter
     * @param integer $width
     * @return string
     */
    public function getBrowserPath($path, $filter, $width)
    {
        return $this->imagine->getBrowserPath($path, $filter, [
            'relative_resize' => [
                'widen' => $width,
            ],
        ]);
    }

    /**
     * @param ImageResizer $resizer
     *
     * @return $this
     */
    public function setResizer(ImageResizer $resizer = null)
    {
        $this->resizer = $resizer ?: $this;

        return $this;
    }


}
