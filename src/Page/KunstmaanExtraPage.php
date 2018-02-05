<?php

namespace ArsThanea\KunstmaanExtraBundle\Page;

use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class KunstmaanExtraPage extends AbstractPage
{
    const PAGE = true;

    /**
     * @return string — without the "Bundle" suffix
     */
    abstract protected function getBundleName();

    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return [];
    }


    public function getDefaultAdminType()
    {
        $type = substr(str_replace('\\Entity\\', '\\Form\\', $this->getType()), 0, - strlen('Page')) . 'AdminType';

        if (class_exists($type)) {
            return $type;
        }

        return parent::getDefaultAdminType();
    }

    public function getDefaultView()
    {
        $page = $this->getPageName();

        return sprintf('@%s/Pages/%s/%s.html.twig', $this->getBundleName(), substr($page, 0, - strlen('Page')), $page);
    }

    public function service(ContainerInterface $container, Request $request, RenderContext $context)
    {
        $context->exchangeArray($context->getArrayCopy() + $container->get('kunstamaan_extra.page_context')->getPageContext($this));
    }

    /**
     * Page type. It should be distinct among all pages. You may just return `self::class`
     *
     * @return string
     */
    public function getType()
    {
        return ClassLookup::getClass($this);
    }

    private function getPageName()
    {
        $type = array_reverse(explode("\\", $this->getType()));

        return reset($type);
    }
}
