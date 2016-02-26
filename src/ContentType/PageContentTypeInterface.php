<?php


namespace ArsThanea\KunstmaanExtraBundle\ContentType;


interface PageContentTypeInterface
{

    /**
     * Maps:
     *   article -> Acme\BundleName\Entity\Pages\ArticlePage
     *
     * @param string $pageName
     * @return string
     */
    public function getContentTypeClass($pageName);

    /**
     * Maps Page class name to a human readable name
     *   Acme\BundleName\Entity\Pages\ArticlePage -> article
     *
     * @param string $typeName
     *
     * @return string
     */
    public function getFriendlyName($typeName);

    /**
     * @return array
     */
    public function getAllContentTypeClasses();


}