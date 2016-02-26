<?php

namespace ArsThanea\KunstmaanExtraBundle\Twig;

use ArsThanea\KunstmaanExtraBundle\ContentType\PageContentTypeInterface;
use ArsThanea\KunstmaanExtraBundle\SiteTree\HasRefInterface;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

class ContentTypeTwigExtension extends \Twig_Extension
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PageContentTypeInterface
     */
    private $contentType;

    public function __construct(EntityManagerInterface $em, PageContentTypeInterface $contentType)
    {
        $this->em = $em;
        $this->contentType = $contentType;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            'page_type_name' => new \Twig_SimpleFunction('page_type_name', function ($page) {
                if ($page instanceof HasNodeInterface) {
                    $page = ClassLookup::getClass($page);
                } elseif ($page instanceof HasRefInterface) {
                    $page = $page->getRefName();
                }

                return $this->contentType->getFriendlyName($page);
            })
        ];
    }


    public function getFilters()
    {
        $filterFactory = function ($className) {
            return function ($nodes) use ($className) {
                $nodes = is_array($nodes) ? $nodes : ($nodes ? iterator_to_array($nodes) : []);
                return array_filter($nodes, function ($item) use ($className) {
                    if ($item instanceof HasRefInterface) {
                        return $item->getRefName() === $className;
                    }

                    return $item instanceof $className;
                });
            };
        };

        $pageFetcher = function ($className) {
            return function ($id) use ($className) {
                return $this->em->getRepository($className)->find($id);
            };
        };

        $pageFetchers = $this->pageFetchersConfiguration();

        $filterFactories = $this->filterFactoriesConfiguration();

        return array_combine(array_keys($pageFetchers), array_map(function ($name, $class) use ($pageFetcher) {
            return new \Twig_SimpleFilter($name, $pageFetcher($class));
        }, array_keys($pageFetchers), $pageFetchers))

        + array_combine(array_keys($filterFactories), array_map(function ($name, $class) use ($filterFactory) {
            return new \Twig_SimpleFilter($name, $filterFactory($class));
        }, array_keys($filterFactories), $filterFactories));
    }

    public function getTests()
    {
        $callbackFactory = function ($className ) {
            return function ($node) use ($className) {
                return ($node instanceof $className)
                    || ($node instanceof Node && $node->getRefEntityName() === $className)
                    || ($node instanceof HasRefInterface && $node->getRefName() === $className);
            };
        };

        return array_map(function ($name, $class) use ($callbackFactory) {
            return new \Twig_SimpleTest($name, $callbackFactory($class));
        }, array_keys($this->typesConfiguration()), $this->typesConfiguration());
    }

    /**
     * Keys should match this format: to_*, as in `{{ page_url(pageRef | to_article) }}`
     *
     * @return array name to class for factory that converts refId to page
     */
    private function pageFetchersConfiguration()
    {
        return $this->formatItems('to_%s');
    }

    /**
     * Keys should match this format: *_pages, as in `{% for page in menu.children|article_pages %}`
     *
     * @return array name to class for factory that filters collection by given type
     */
    private function filterFactoriesConfiguration()
    {
        return $this->formatItems('%s_pages');
    }

    /**
     * Keys should match this format: *_page, as in `{% if page is article_page %}`
     *
     * @return array name to class for factory that determines if a variable is of given type
     */
    public function typesConfiguration()
    {
        return $this->formatItems('%s_page');
    }

    private function formatItems($string)
    {
        $items = $this->contentType->getAllContentTypeClasses();

        return array_combine(
            array_map(function ($name) use ($string) { return sprintf($string, $name); }, array_keys($items)),
            $items
        );

    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'kunstmaan_extra_content_type';
    }
}
