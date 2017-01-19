<?php

namespace ArsThanea\KunstmaanExtraBundle\Imagine;

class ImgixSerializer
{
    private $prefix = '_imgix/';

    private $urlsafe = [
        '+' => '-',
        '/' => '_',
        '=' => '',
    ];

    public function __construct($prefix = null)
    {
        $this->prefix = trim($prefix ?: $this->prefix, '/') . '/';
    }

    public function serialize($path, array $transformations)
    {
        $data = strtr(base64_encode(http_build_query($transformations)), $this->urlsafe);

        return sprintf("/%s/%s/%s", trim($this->prefix, '/'), $data, ltrim($path, '/'));
    }

    public function deserialize($path)
    {
        if ($this->prefix !== substr($path, 0, strlen($this->prefix))) {
            return null;
        }

        list ($data, $path) = explode('/', substr($path, strlen($this->prefix)), 2);

        return $path . '?' . base64_decode(strtr($data, array_flip(array_filter($this->urlsafe))));
    }
}
