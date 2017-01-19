<?php

namespace ArsThanea\KunstmaanExtraBundle\Imagine;

use Liip\ImagineBundle\Binary\Loader\LoaderInterface;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;

class ImgixDataLoader implements LoaderInterface
{

    /**
     * @var ImgixSerializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $source;

    /**
     * @param ImgixSerializer $serializer
     * @param $source
     */
    public function __construct(ImgixSerializer $serializer, $source)
    {
        $this->serializer = $serializer;
        $this->source = rtrim($source, '/') . '/';
    }


    /**
     * Retrieve the Image represented by the given path.
     *
     * The path may be a file path on a filesystem, or any unique identifier among the storage engine implemented by this Loader.
     *
     * @param mixed $path
     *
     * @return \Liip\ImagineBundle\Binary\BinaryInterface|string An image binary content
     */
    public function find($path)
    {
        $imgix = $this->serializer->deserialize($path);
        if ($imgix) {
            return file_get_contents($this->source . $imgix);
        }

        throw new NotLoadableException;
    }
}
