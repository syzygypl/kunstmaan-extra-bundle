<?php

namespace ArsThanea\KunstmaanExtraBundle\Twig;

use ArsThanea\KunstmaanExtraBundle\ContentCategory\Category;
use ArsThanea\KunstmaanExtraBundle\ContentCategory\ContentCategoryInterface;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
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
            new \Twig_SimpleFunction('current_category', [$this, 'getCurrentCategory']),
            new \Twig_SimpleFunction('root_category', [$this, 'getRootCategory']),
            new \Twig_SimpleFunction('main_category', [$this, 'getMainCategory']),
            new \Twig_SimpleFunction('parent_category', [$this, 'getParentCategory']),
            new \Twig_SimpleFunction('get_breadcrumbs', [$this, 'getBreadcrumbs']),
        ];
    }

    public function getRootCategory(HasNodeInterface $page)
    {
        return $this->categoryService->getRootCategory($page);
    }

    public function getMainCategory(HasNodeInterface $page, $hidden = false)
    {
        return $this->categoryService->getMainCategory($page, $hidden);
    }

    public function getParentCategory(HasNodeInterface $page, $includeHidden = false)
    {
        return $this->categoryService->getParentCategory($page, $includeHidden);
    }

    public function getCurrentCategory(HasNodeInterface $page, $includeHidden = true)
    {
        return $this->categoryService->getCurrentCategory($page, $includeHidden);
    }

    public function getBreadcrumbs(HasNodeInterface $page, $includeHidden = false)
    {
        return $this->categoryService->getBreadcrumbs($page, $includeHidden);
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
