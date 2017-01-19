<?php

namespace ArsThanea\KunstmaanExtraBundle\Imagine;

interface ImageResizer
{
    /**
     * @param string  $path
     * @param string  $filter
     * @param integer $width
     * @return string
     */
    public function getBrowserPath($path, $filter, $width);
}
