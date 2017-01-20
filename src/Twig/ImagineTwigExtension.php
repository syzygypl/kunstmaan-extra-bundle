<?php

namespace ArsThanea\KunstmaanExtraBundle\Twig;

use ArsThanea\KunstmaanExtraBundle\Imagine\ImgixFilter;
use ArsThanea\KunstmaanExtraBundle\Imagine\SrcsetCalculator;

class ImagineTwigExtension extends \Twig_Extension
{
    /**
     * @var ImgixFilter
     */
    private $imgix;

    /**
     * @var SrcsetCalculator
     */
    private $srcsetCalculator;

    public function __construct(SrcsetCalculator $srcsetCalculator, ImgixFilter $imgix = null)
    {
        $this->srcsetCalculator = $srcsetCalculator;
        $this->imgix = $imgix;
    }


    public function getFilters()
    {
        return array_filter([
            new \Twig_SimpleFilter('srcset', [$this, 'getSrcset']),
            $this->imgix ? new \Twig_SimpleFilter('imgix_filter', [$this, 'imgixFilter']) : null,
        ]);
    }


    public function srcsetFilter($media, $filter = null)
    {
        $values = $this->srcsetCalculator->getSrcset($media, $filter);

        if (1 >= sizeof($values)) {
            return '';
        }

        $srcset = array_map(function ($width, $url) {
            return sprintf('%s %dw', $url, $width);
        }, array_keys($values), $values);

        return implode(', ', $srcset);
    }

    public function imgixFilter($path, $name, array $transformations = [])
    {
        if (null === $this->imgix) {
            throw new \RuntimeException('You need to configure imgix first to use this filter!');
        }

        if ("" === (string)$path) {
            return "";
        }

        return $this->imgix->imgixFilter($path, $name, $transformations);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'kunstmaan_extra_imagine';
    }
}
