<?php

namespace ArsThanea\KunstmaanExtraBundle\Twig;

use ArsThanea\KunstmaanExtraBundle\SiteTree\Navigation\Navigation;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;

class NavigationTwigExtension extends \Twig_Extension
{

    /**
     * @var Navigation
     */
    private $navigation;

    /**
     * @param Navigation $navigation
     */
    public function __construct(Navigation $navigation)
    {
        $this->navigation = $navigation;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_navigation_next', [$this, 'getNextPage']),
            new \Twig_SimpleFunction('get_navigation_prev', [$this, 'getPrevPage']),
            new \Twig_SimpleFunction('get_navigation_siblings', [$this, 'getNavigationSiblings']),
        ];
    }

    public function getNextPage(HasNodeInterface $page)
    {
        return $this->navigation->getNextPage($page);
    }

    public function getPrevPage(HasNodeInterface $page)
    {
        return $this->navigation->getPreviousPage($page);
    }

    public function getNavigationSiblings(HasNodeInterface $page)
    {
        return [
            'prev' => $this->getPrevPage($page),
            'next' => $this->getNextPage($page),
        ];
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'kunstmaan_extra_navigation';
    }
}
