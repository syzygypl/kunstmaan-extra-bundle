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

    public function getNextPage(HasNodeInterface $page, $loop = false)
    {
        return $this->navigation->getNextPage($page, $loop);
    }

    public function getPrevPage(HasNodeInterface $page, $loop = false)
    {
        return $this->navigation->getPreviousPage($page, $loop);
    }

    public function getNavigationSiblings(HasNodeInterface $page, $loop = false)
    {
        return [
            'prev' => $this->getPrevPage($page, $loop),
            'next' => $this->getNextPage($page, $loop),
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
