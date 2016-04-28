<?php

namespace ArsThanea\KunstmaanExtraBundle\ContentType;

use Kunstmaan\NodeBundle\Helper\PagesConfiguration;

class ContentTypeService implements PageContentTypeInterface
{
    /**
     * @var PagesConfiguration
     */
    private $pagesConfiguration;

    /**
     * @var array
     */
    private $allTypes;

    /**
     * SiteTreeTwigExtension constructor.
     *
     * @param PagesConfiguration $pagesConfiguration
     */
    public function __construct(PagesConfiguration $pagesConfiguration)
    {
        $this->pagesConfiguration = $pagesConfiguration;
    }

    /**
     * Maps:
     *   article -> ArsThanea\BundleName\Entity\Pages\ArticlePage
     *
     * @param string $pageName
     * @return string
     */
    public function getContentTypeClass($pageName)
    {
        $pageName = $this->getFriendlyName($pageName);

        $allTypes = $this->getAllContentTypeClasses();

        return isset($allTypes[$pageName]) ? $allTypes[$pageName] : null;
    }

    public function getFriendlyName($typeName)
    {
        $parts = explode("\\", $typeName);
        $name = end($parts);
        $name = preg_replace('/(?=[A-Z])/', '_', $name);
        $name = strtolower($name);
        $name = str_replace('_page', '', $name);
        $name = trim($name, '_');

        return $name;
    }

    /**
     * @return array
     */
    public function getAllContentTypeClasses()
    {
        if (null === $this->allTypes) {
            $homePages = array_keys($this->pagesConfiguration->getHomepageTypes());
            $this->allTypes = array_unique(
                array_reduce($homePages, function ($allTypes, $homePage) {
                    return array_merge($allTypes, iterator_to_array($this->getTypesDeep($homePage)));
                }, $homePages)
            );

            $this->allTypes = array_combine(array_map([$this, 'getFriendlyName'], $this->allTypes), $this->allTypes);
        }

        return $this->allTypes;
    }

    private function getTypesDeep($refName, $exclude = [])
    {
        foreach ($this->pagesConfiguration->getPossibleChildTypes($refName) as $type) {
            $type = $type['class'];
            if (in_array($type, $exclude)) {
                continue;
            }

            yield $type;

            $exclude[] = $type;

            foreach ($this->getTypesDeep($type, $exclude) as $subType) {
                $exclude[] = $subType;
                yield $subType;
            }
        }
    }
}
