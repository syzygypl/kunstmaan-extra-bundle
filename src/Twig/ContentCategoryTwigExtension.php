<?php

namespace ArsThanea\KunstmaanExtraBundle\Twig;

use ArsThanea\KunstmaanExtraBundle\ContentCategory\Category;
use ArsThanea\KunstmaanExtraBundle\ContentCategory\ContentCategoryInterface;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;

class ContentCategoryTwigExtension extends \Twig_Extension
{
    /**
     * @var ContentCategoryInterface
     */
    private $categoryService;

    /**
     * @var SlugifierInterface
     */
    private $slugifier;

    public function __construct(ContentCategoryInterface $categoryService, SlugifierInterface $slugifier)
    {
        $this->categoryService = $categoryService;
        $this->slugifier = $slugifier;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('main_category', [$this->categoryService, 'getMainCategory']),
            new \Twig_SimpleFunction('parent_category', [$this->categoryService, 'getParentCategory']),
            new \Twig_SimpleFunction('get_breadcrumbs', [$this->categoryService, 'getBreadcrumbs']),
        ];
    }

    public function getTests()
    {
        return [
            'under page' => new \Twig_SimpleTest('under page', [$this, 'isUnderPage']),
        ];
    }

    public function isUnderPage(AbstractPage $page, $name)
    {

        $slug = $this->slugifier->slugify($name);
        $breadcrumbs = $this->categoryService->getBreadcrumbs($page, true);

        return array_reduce($breadcrumbs, function ($match, Category $parent) use ($slug) {
            if ($match) {
                return true;
            }

            return substr($parent->getSlug(), 0, -strlen($slug)) === $slug
                || $this->slugifier->slugify($parent->getTitle()) === $slug;
        });

    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'content_category';
    }
}